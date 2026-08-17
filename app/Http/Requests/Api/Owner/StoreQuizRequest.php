<?php

namespace App\Http\Requests\Api\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // enforced by 'permission.api:quizzes.create' route middleware
    }

    public function rules(): array
    {
        return [
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'attach_type'        => 'nullable|in:course_lesson,darsat,none',
            'attach_id'          => 'nullable|integer',
            'passing_percentage' => 'required|integer|min:1|max:100',
            'status'             => 'required|in:draft,published',
            'starts_at'          => 'nullable|date',
            'duration_minutes'   => 'nullable|integer|min:1|max:600',
        ];
    }
}
