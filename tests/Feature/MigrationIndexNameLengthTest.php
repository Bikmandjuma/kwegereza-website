<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Real bug hit in a production Windows/MySQL run (not caught by this
 * project's own test suite, which runs against sqlite - sqlite has no
 * identifier length limit at all). The live_class_participants
 * migration's auto-generated composite index name (76 chars) exceeded
 * MySQL's 64-character identifier limit, causing migrate to fail
 * outright with "Identifier name ... too long" - a hard failure that
 * stops the whole migration run partway through. Fixed by giving that
 * specific index an explicit short name; this test is a permanent
 * sweep so a future migration with a similarly long table+column
 * combination fails fast in CI instead of only showing up the first
 * time someone actually runs migrate against real MySQL.
 */
class MigrationIndexNameLengthTest extends TestCase
{
    /**
     * First draft of this test used a single preg_match to grab "the"
     * table name from the whole file, then applied it to every index
     * found anywhere in that file — wrong the moment a migration
     * defines more than one table (exactly this migration's shape:
     * live_classes then live_class_participants). It silently checked
     * every live_class_participants index against the shorter
     * 'live_classes' name and passed even when I deliberately reverted
     * the fix to prove the test worked. Fixed by splitting the file into
     * per-table Schema::create blocks first, so each index is measured
     * against the table it's actually defined under.
     */
    public function test_no_migration_produces_an_auto_generated_index_or_unique_name_over_64_characters(): void
    {
        $violations = [];

        foreach (glob(database_path('migrations/*.php')) as $file) {
            $content = file_get_contents($file);

            // Split on each Schema::create(...) call so a multi-table
            // migration file is checked block-by-block, not as one blob.
            $blocks = preg_split('/(?=Schema::create\()/', $content);

            foreach ($blocks as $block) {
                if (! preg_match('/Schema::create\([\'"](\w+)[\'"]/', $block, $tableMatch)) {
                    continue;
                }
                $table = $tableMatch[1];

                if (preg_match_all('/->(index|unique)\(\[(.*?)\]\)/s', $block, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $type = $match[1];
                        $columns = array_map(
                            fn ($c) => trim($c, " \t\n\r\0\x0B'\""),
                            explode(',', $match[2])
                        );
                        $autoName = $table.'_'.implode('_', $columns).'_'.$type;

                        if (strlen($autoName) > 64) {
                            $violations[] = basename($file)." (table: {$table}): ".$autoName.' ('.strlen($autoName).' chars)';
                        }
                    }
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "The following migrations produce an auto-generated index/unique name over MySQL's 64-character limit:\n".implode("\n", $violations)
        );
    }
}
