<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'description'         => $this->description,
            'passing_percentage'  => $this->passing_percentage,
            'status'              => $this->status,
            'starts_at'           => $this->starts_at,
            'duration_minutes'    => $this->duration_minutes,
            'quizzable_type'      => $this->quizzable_type,
            'quizzable_id'        => $this->quizzable_id,
            'questions_count'     => $this->whenCounted('questions'),
            'attempts_count'      => $this->whenCounted('attempts'),
            'total_points'        => $this->totalPoints(),
            'questions'           => QuizQuestionResource::collection($this->whenLoaded('questions')),
            'created_at'          => $this->created_at,
        ];
    }
}
