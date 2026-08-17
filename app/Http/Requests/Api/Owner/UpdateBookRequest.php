<?php

namespace App\Http\Requests\Api\Owner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // enforced by 'permission.api:books.update' route middleware
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'book'        => 'nullable|mimes:pdf|max:51200',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'required|in:draft,published',
            'is_downloadable' => 'sometimes|boolean',
        ];
    }
}
