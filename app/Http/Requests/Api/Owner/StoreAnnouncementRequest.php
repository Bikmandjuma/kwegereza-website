<?php

namespace App\Http\Requests\Api\Owner;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // enforced by 'permission.api:amatangazo.create' route middleware
    }

    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'presenter'    => 'nullable|string|max:255',
            'status'       => 'required|in:live,upcoming,done',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published' => 'sometimes|boolean',
        ];
    }
}
