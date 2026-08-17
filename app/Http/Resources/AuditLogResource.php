<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'entity_type' => class_basename($this->entity_type),
            'entity_id' => $this->entity_id,
            'entity_label' => $this->entity_label,
            'before' => $this->before,
            'after' => $this->after,
            'ip' => $this->ip,
            'owner' => $this->whenLoaded('owner', fn () => $this->owner ? [
                'id' => $this->owner->id,
                'name' => trim($this->owner->firstname.' '.$this->owner->lastname),
            ] : null),
            'created_at' => $this->created_at,
        ];
    }
}
