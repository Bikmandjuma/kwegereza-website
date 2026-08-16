<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Support\Facades\Storage;

/**
 * Backups phase: BackupService itself is already well-built and honestly
 * documents its own limitations (pure-PHP dump, no shell_exec dependency,
 * doesn't capture views/triggers). What it does NOT address — and what
 * this phase surfaces rather than silently ports around — is that
 * backups are written to the 'local' disk, the SAME kind of ephemeral,
 * non-persistent storage the Media Persistence fix addressed for user
 * uploads. A backup's entire purpose is surviving data loss on the
 * server that holds it; a backup stored on that same server's ephemeral
 * disk provides no protection against the exact failure mode (a Railway
 * restart/redeploy) it exists to guard against. Not fixed in this pass —
 * a genuinely separate architectural question (backups likely belong in
 * a different bucket/location than user uploads) deserving its own
 * dedicated look rather than a rushed change here.
 */
class BackupController extends Controller
{
    public function __construct(private BackupService $backups)
    {
    }

    public function index()
    {
        return response()->json(['success' => true, 'message' => null, 'data' => $this->backups->listBackups()]);
    }

    public function store()
    {
        set_time_limit(120);

        try {
            $filename = $this->backups->createBackup();

            return response()->json(['success' => true, 'message' => "Backup yakozwe: {$filename}", 'data' => ['filename' => $filename]], 201);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Backup ntiyakunze: '.$e->getMessage(), 'data' => null], 500);
        }
    }

    public function download(string $filename)
    {
        if (! preg_match('/^backup-[\d_-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = 'backups/'.$filename;
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }

    public function destroy(string $filename)
    {
        if ($this->backups->deleteBackup($filename)) {
            return response()->json(['success' => true, 'message' => 'Backup yasibwe.', 'data' => null]);
        }

        return response()->json(['success' => false, 'message' => 'Ntibyashobotse gusiba iyi backup.', 'data' => null], 422);
    }
}
