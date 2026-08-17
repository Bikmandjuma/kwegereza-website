<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Owner-side resource: includes is_correct/short_answer since the person
 * managing the quiz needs to see and edit the correct answer. NEVER reuse
 * this for a student-facing endpoint (spec section 12: "Do not expose
 * correct answers unnecessarily through the API") — a future student-quiz
 * API needs its own resource that strips is_correct and short_answer.
 */
class QuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'question'           => $this->question,
            'type'               => $this->type,
            'short_answer'       => $this->short_answer,
            'points'             => $this->points,
            'order'              => $this->order,
            'time_limit_seconds' => $this->time_limit_seconds,
            'answers'            => $this->whenLoaded('answers', fn () => $this->answers->map(fn ($a) => [
                'id'          => $a->id,
                'answer_text' => $a->answer_text,
                'is_correct'  => $a->is_correct,
                'order'       => $a->order,
            ])),
        ];
    }
}
