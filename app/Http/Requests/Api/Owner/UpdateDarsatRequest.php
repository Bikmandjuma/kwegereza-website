<?php

namespace App\Http\Requests\Api\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDarsatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // enforced by 'permission.api:darsat.update' route middleware
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'teachers'    => 'required|exists:owners,id',
            'type'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'audio'       => 'nullable|mimes:mp3,wav,ogg,m4a|max:51200',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
        ];
    }
}
