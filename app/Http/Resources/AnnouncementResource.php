<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'presenter'    => $this->presenter,
            'status'       => $this->status,
            'image_url'    => $this->imageUrl(),
            'is_published' => $this->is_published,
            'published_at' => $this->published_at,
            'created_at'   => $this->created_at,
        ];
    }
}
