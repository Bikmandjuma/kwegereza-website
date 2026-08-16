<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'guest_id'    => $this->guest_id,
            'sender_type' => $this->sender_type,
            'sender_name' => $this->sender_name,
            'message'     => $this->message,
            'is_read'     => (bool) $this->is_read,
            'created_at'  => $this->created_at,
        ];
    }
}
