<?php

namespace App\Http\Requests\Api\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // enforced by 'permission.api:quizzes.create' route middleware
    }

    public function rules(): array
    {
        return [
            'question'           => 'required|string',
            'type'               => 'required|in:multiple_choice,true_false,short_answer',
            'points'             => 'required|integer|min:1|max:100',
            'time_limit_seconds' => 'nullable|integer|min:5|max:1800',
            'short_answer'       => 'required_if:type,short_answer|nullable|string|max:255',
            'answers'            => 'required_if:type,multiple_choice|nullable|array',
            'answers.*'          => 'nullable|string|max:255',
            'correct'            => 'required_if:type,multiple_choice,true_false|nullable',
        ];
    }
}
