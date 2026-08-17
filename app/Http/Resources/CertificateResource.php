<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'certificate_number' => $this->certificate_number,
            'verification_code' => $this->verification_code,
            'title' => $this->title,
            'issued_at' => $this->issued_at,
            'user' => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id, 'name' => trim($this->user->firstname.' '.$this->user->lastname),
            ] : null),
            'course' => $this->whenLoaded('course', fn () => $this->course ? ['id' => $this->course->id, 'title' => $this->course->title] : null),
        ];
    }
}
