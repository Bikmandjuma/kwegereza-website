<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'description'      => $this->description,
            'thumbnail_url'    => $this->thumbnailUrl(),
            'status'           => $this->status,
            'published_at'     => $this->published_at,
            'lessons_count'    => $this->whenCounted('lessons'),
            'enrollments_count' => $this->whenCounted('enrollments'),
            'lessons'          => CourseLessonResource::collection($this->whenLoaded('lessons')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
