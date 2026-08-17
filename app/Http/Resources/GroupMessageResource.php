<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'group'       => $this->group,
            'sender_type' => $this->sender_type,
            'sender_id'   => $this->sender_id,
            'sender_name' => $this->sender_name,
            'message'     => $this->message,
            'created_at'  => $this->created_at,
            'parent_id'   => $this->parent_id,
            'parent'      => $this->whenLoaded('parent', fn () => [
                'id' => $this->parent->id,
                'sender_name' => $this->parent->sender_name,
                'message' => $this->parent->message,
            ]),
            'is_pinned'   => $this->isPinned(),
            'pinned_at'   => $this->pinned_at,
            'reactions'   => $this->whenLoaded('reactions', fn () => $this->reactions
                ->groupBy('emoji')
                ->map(fn ($group) => $group->count())
            ),
        ];
    }
}
