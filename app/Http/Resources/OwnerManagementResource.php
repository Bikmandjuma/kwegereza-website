<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerManagementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'title' => $this->title,
            'role' => $this->role,
            'image_url' => \App\Support\FileUrl::resolve($this->image, 'images/users', 'Guest/images/users'),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('slug')),
        ];
    }
}
