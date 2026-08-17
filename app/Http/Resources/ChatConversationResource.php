<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'guest_id'       => $this->guest_id,
            'sender_name'    => $this->sender_name,
            'last_message'   => $this->last_message,
            'last_message_at' => $this->last_message_at,
            'unread_count'   => $this->unread_count,
            'online'         => $this->online,
            'typing'         => $this->typing,
        ];
    }
}
