<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Attach to any model via `ModelClass::observe(AuditObserver::class)` in
 * AppServiceProvider::boot(). Deliberately generic — no per-model config
 * needed beyond that one line, so adding auditing to a new model later is
 * a single line, not a new class.
 */
class AuditObserver
{
    private const SENSITIVE_KEYS = ['password', 'remember_token', 'api_token'];

    public function created(Model $model): void
    {
        $this->log('created', $model, null, $this->clean($model->getAttributes()));
    }

    /**
     * Every student page load runs UpdateLastActive, which updates
     * last_active_at unconditionally — before this fix, that meant every
     * single request from every active student generated its own audit
     * log entry, purely from a routine timestamp with no actual
     * meaningful change behind it. Confirmed with a real test (spreading
     * requests a realistic number of seconds apart, not back-to-back —
     * fast, same-second test requests can accidentally hide this, since
     * Eloquent's dirty-checking correctly skips firing 'updated' when a
     * second-precision column doesn't actually change value): 5 ordinary
     * page loads produced 5 separate audit rows. Left unfixed, this would
     * grow unbounded in production and drown out genuinely meaningful
     * entries (an admin editing a Book, say) in noise. Purely cosmetic
     * fields like this are excluded from triggering a log entry, while
     * still catching them if they change ALONGSIDE something meaningful.
     */
    private const TRIVIAL_ONLY_FIELDS = ['last_active_at', 'updated_at'];

    public function updated(Model $model): void
    {
        $changes = $this->clean($model->getChanges());

        if (empty(array_diff(array_keys($changes), self::TRIVIAL_ONLY_FIELDS))) {
            return;
        }

        $this->log(
            'updated',
            $model,
            $this->clean($model->getOriginal()),
            $changes
        );
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, $this->clean($model->getAttributes()), null);
    }

    private function log(string $action, Model $model, ?array $before, ?array $after): void
    {
        AuditLog::create([
            'owner_id'     => Auth::guard('owner')->id(),
            'action'       => $action,
            'entity_type'  => get_class($model),
            'entity_id'    => $model->getKey(),
            'entity_label' => $this->labelFor($model),
            'before'       => $before,
            'after'        => $after,
            'ip'           => Request::ip(),
        ]);
    }

    private function labelFor(Model $model): ?string
    {
        foreach (['title', 'name', 'question'] as $field) {
            if (!empty($model->{$field})) {
                return (string) $model->{$field};
            }
        }

        return null;
    }

    private function clean(array $attributes): array
    {
        return collect($attributes)
            ->except(self::SENSITIVE_KEYS)
            ->toArray();
    }
}
