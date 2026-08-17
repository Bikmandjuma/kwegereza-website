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

    public function updated(Model $model): void
    {
        $this->log(
            'updated',
            $model,
            $this->clean($model->getOriginal()),
            $this->clean($model->getChanges())
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
