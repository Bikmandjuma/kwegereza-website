<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->data['type'] ?? null,
            'title'      => $this->data['title'] ?? null,
            'message'    => $this->data['message'] ?? null,
            'url'        => $this->data['url'] ?? null,
            'is_read'    => ! is_null($this->read_at),
            'read_at'    => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
