<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'author'          => $this->author,
            'category'        => $this->category,
            'description'     => $this->description,
            'cover_url'       => $this->coverUrl(),
            'book_file_url'   => $this->bookFileUrl(),
            'status'          => $this->status,
            'is_downloadable' => $this->is_downloadable,
            'views'           => $this->views,
            'downloads'       => $this->downloads,
            'published_at'    => $this->published_at,
            'created_at'      => $this->created_at,
        ];
    }
}
