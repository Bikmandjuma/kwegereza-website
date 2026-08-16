<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseLessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'course_id'   => $this->course_id,
            'title'       => $this->title,
            'description' => $this->description,
            'content'     => $this->content,
            'darsat_id'   => $this->darsat_id,
            'order'       => $this->order,
            'is_required' => $this->is_required,
        ];
    }
}
