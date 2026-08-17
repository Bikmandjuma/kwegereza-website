<?php

namespace App\Http\Requests\Api\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // enforced by 'permission.api:quizzes.update' route middleware
    }

    public function rules(): array
    {
        return [
            'question'           => 'required|string',
            'points'             => 'required|integer|min:1|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
        ];
    }
}
