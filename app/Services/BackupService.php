<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Generates a SQL dump using pure PHP + Laravel's DB facade — deliberately
 * NOT shelling out to `mysqldump`. Two reasons:
 *
 * 1. `mysqldump` may not even be installed in this app's hosting container
 *    (Railway's PHP image is not guaranteed to include MySQL client tools),
 *    and shell_exec()/exec() are commonly disabled entirely on shared or
 *    security-hardened PHP hosting. A backup feature that silently fails
 *    because a binary isn't present is worse than one that just works.
 * 2. This way it works identically regardless of hosting provider.
 *
 * Honest limitations, stated in the README rather than hidden: this
 * captures table structure + data as INSERT statements. It does NOT
 * capture views, stored procedures, triggers, or events — this app doesn't
 * use any of those, but if that ever changes, this backup would miss them.
 * Chunked reads assume every table has an `id` primary key column, which
 * is true for every table in this app (every migration uses $table->id()).
 */
class BackupService
{
    private const CHUNK_SIZE = 500;

    public function createBackup(): string
    {
        $filename = 'backup-' . now()->format('Y-m-d_His') . '.sql';
        $tables = $this->getAllTables();

        $sql = "-- Kwegereza Islam Umuryango database backup\n";
        $sql .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $sql .= "-- Tables: " . count($tables) . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        Storage::disk('local')->put('backups/' . $filename, $sql);

        foreach ($tables as $table) {
            $this->appendTableToBackup($table, $filename);
        }

        Storage::disk('local')->append('backups/' . $filename, "\nSET FOREIGN_KEY_CHECKS=1;\n");

        return $filename;
    }

    private function getAllTables(): array
    {
        $dbName = DB::connection()->getDatabaseName();
        $rows = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . $dbName;

        return array_map(fn($row) => $row->$key, $rows);
    }

    private function appendTableToBackup(string $table, string $filename): void
    {
        $createStatement = DB::select("SHOW CREATE TABLE `{$table}`");
        $createSql = $createStatement[0]->{'Create Table'} ?? null;

        $chunk = "-- --------------------------------------------------------\n";
        $chunk .= "-- Table: {$table}\n";
        $chunk .= "-- --------------------------------------------------------\n\n";
        $chunk .= "DROP TABLE IF EXISTS `{$table}`;\n";
        $chunk .= $createSql . ";\n\n";

        Storage::disk('local')->append('backups/' . $filename, $chunk);

        $hasIdColumn = \Illuminate\Support\Facades\Schema::hasColumn($table, 'id');

        $query = DB::table($table);

        if ($hasIdColumn) {
            $query->chunkById(self::CHUNK_SIZE, function ($rows) use ($table, $filename) {
                $this->writeRowsAsInserts($rows, $table, $filename);
            });
        } else {
            // No `id` column (shouldn't happen in this app, but degrade
            // safely rather than error out) — read without chunking.
            $rows = $query->get();
            $this->writeRowsAsInserts($rows, $table, $filename);
        }

        Storage::disk('local')->append('backups/' . $filename, "\n");
    }

    private function writeRowsAsInserts($rows, string $table, string $filename): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $columns = array_keys((array) $rows->first());
        $columnList = '`' . implode('`, `', $columns) . '`';

        $valuesSql = [];
        foreach ($rows as $row) {
            $values = array_map(fn($value) => $this->escapeValue($value), (array) $row);
            $valuesSql[] = '(' . implode(', ', $values) . ')';
        }

        $insert = "INSERT INTO `{$table}` ({$columnList}) VALUES\n" . implode(",\n", $valuesSql) . ";\n";

        Storage::disk('local')->append('backups/' . $filename, $insert);
    }

    private function escapeValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_numeric($value) && !is_string($value)) {
            return (string) $value;
        }

        return "'" . str_replace(
            ["\\", "'", "\0", "\n", "\r", "\x1a"],
            ["\\\\", "\\'", "\\0", "\\n", "\\r", "\\Z"],
            (string) $value
        ) . "'";
    }

    /**
     * @return array<int, array{filename: string, size: int, created_at: \Carbon\Carbon}>
     */
    public function listBackups(): array
    {
        $files = Storage::disk('local')->files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (!str_ends_with($file, '.sql')) {
                continue;
            }

            $backups[] = [
                'filename'   => basename($file),
                'size'       => Storage::disk('local')->size($file),
                'created_at' => \Illuminate\Support\Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file)),
            ];
        }

        usort($backups, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $backups;
    }

    public function deleteBackup(string $filename): bool
    {
        // Guard against path traversal — only ever touch files literally
        // named like our own generated backups.
        if (!preg_match('/^backup-[\d_-]+\.sql$/', $filename)) {
            return false;
        }

        return Storage::disk('local')->delete('backups/' . $filename);
    }
}
