<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'title' => $this->title,
            'bio' => $this->bio,
            'image' => $this->image,
            // Was hardcoded as asset('storage/avatars/'.$this->image) —
            // correct only for the local 'public' disk's symlinked path.
            // Once FILESYSTEM_DISK=s3 is set for a real deployment, that
            // would keep pointing at a local path that no longer
            // corresponds to where the file actually lives, breaking
            // avatar display even though the upload itself (fixed above,
            // in OwnerProfileService) would correctly persist. Uses the
            // same disk-aware helper Book/Inyandiko already rely on for
            // exactly this reason.
            'image_url' => \App\Support\FileUrl::resolve($this->image, 'avatars'),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('slug')),
        ];
    }
}
