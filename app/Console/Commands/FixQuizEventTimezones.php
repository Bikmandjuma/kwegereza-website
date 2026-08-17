<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Quiz;
use Illuminate\Console\Command;

/**
 * Targeted, one-record-at-a-time repair for a SPECIFIC quiz or event that
 * was saved BEFORE the timezone fix (QuizController::parseStartsAt() /
 * EventController::parseLocalDateTime()).
 *
 * Deliberately NOT a blanket "fix everything" command. There is no way to
 * tell, just by looking at a stored starts_at value, whether it was saved
 * by the OLD broken code (needs a -2 hour correction) or the NEW fixed
 * code (already correct, and would be WRONGLY corrupted by a blanket
 * -2 hour shift). Applying this to the wrong record, or to a record twice,
 * would silently introduce the exact bug it's meant to fix. So this
 * requires you to name the exact record by type and ID, shows you the
 * before/after, and asks for explicit confirmation before saving anything.
 *
 * The simplest and safest fix for most cases remains: just re-open the
 * quiz/event in the admin edit form and re-save its start time — the
 * fixed code will store it correctly. Use this command only for records
 * you can't or don't want to touch through the UI.
 */
class FixQuizEventTimezones extends Command
{
    protected $signature = 'kiu:fix-timezone-data {type : "quiz" or "event"} {id : The specific record ID to correct}';

    protected $description = 'Correct ONE specific quiz or event start time that was saved before the Kigali timezone fix (2-hour shift)';

    public function handle(): int
    {
        $type = strtolower($this->argument('type'));
        $id = (int) $this->argument('id');
        $offsetHours = 2; // Africa/Kigali is UTC+2, with no DST to worry about

        if (!in_array($type, ['quiz', 'event'], true)) {
            $this->error('First argument must be "quiz" or "event".');
            return self::FAILURE;
        }

        $record = $type === 'quiz' ? Quiz::find($id) : Event::find($id);

        if (!$record) {
            $this->error(ucfirst($type) . " #{$id} not found.");
            return self::FAILURE;
        }

        if (!$record->starts_at) {
            $this->error(ucfirst($type) . " #{$id} has no starts_at set, nothing to fix.");
            return self::FAILURE;
        }

        $beforeStart = $record->starts_at->copy();
        $afterStart = $beforeStart->copy()->subHours($offsetHours);

        $beforeEnd = $type === 'event' ? $record->ends_at?->copy() : null;
        $afterEnd = $beforeEnd?->copy()->subHours($offsetHours);

        $this->info(ucfirst($type) . " #{$id}: \"{$record->title}\"");
        $this->line("  starts_at:  {$beforeStart->toDateTimeString()}  ->  {$afterStart->toDateTimeString()}");
        if ($type === 'event' && $beforeEnd) {
            $this->line("  ends_at:    {$beforeEnd->toDateTimeString()}  ->  {$afterEnd->toDateTimeString()}");
        }

        if (!$this->confirm('Apply this correction? Only do this if you are sure this exact record predates the timezone fix.', false)) {
            $this->warn('Cancelled ,nothing was changed.');
            return self::SUCCESS;
        }

        if ($type === 'quiz') {
            $record->update(['starts_at' => $afterStart]);
        } else {
            $record->update(['starts_at' => $afterStart, 'ends_at' => $afterEnd]);
        }

        $this->info('Corrected.');

        return self::SUCCESS;
    }
}
