<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var array $this->resource */
        $data = $this->resource;
        $student = $data['student'];

        return [
            'student' => (new StudentResource($student))->toArray($request),
            'completed_darsat_count' => $data['completed_darsat_count'],
            'recent_quiz_attempts' => $data['recent_quiz_attempts']->map(fn ($attempt) => [
                'id'           => $attempt->id,
                'quiz_title'   => $attempt->quiz?->title,
                'submitted_at' => $attempt->submitted_at,
                'score'        => $attempt->score,
                'percentage'   => $attempt->percentage,
                'passed'       => $attempt->passed,
            ]),
            'certificates' => $data['certificates']->map(fn ($cert) => [
                'id' => $cert->id,
                'title' => $cert->title ?? null,
                'issued_at' => $cert->created_at,
            ]),
            'badges' => $data['badges']->map(fn ($ub) => [
                'id' => $ub->id,
                'badge_name' => $ub->badge?->name,
            ]),
        ];
    }
}
