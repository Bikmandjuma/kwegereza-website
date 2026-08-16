<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_code'      => $this->user_code,
            'firstname'      => $this->firstname,
            'lastname'       => $this->lastname,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'is_blocked'     => (bool) $this->deactivated_at,
            'deactivated_at' => $this->deactivated_at,
            'created_at'     => $this->created_at,
        ];
    }
}
