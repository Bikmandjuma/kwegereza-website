<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DarsatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'type'          => $this->type,
            'description'   => $this->description,
            'audio_url'     => $this->audioUrl(),
            'thumbnail_url' => $this->thumbnailUrl(),
            'status'        => $this->status,
            'plays'         => $this->plays,
            'published_at'  => $this->published_at,
            'teacher'       => $this->whenLoaded('teacher', fn () => [
                'id'   => $this->teacher->id,
                'name' => trim($this->teacher->firstname.' '.$this->teacher->lastname),
            ]),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
