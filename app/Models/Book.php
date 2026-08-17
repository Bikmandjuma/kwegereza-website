<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'author', 'category', 'description', 'book', 'cover_image',
        'status', 'is_downloadable', 'views', 'created_by', 'updated_by', 'published_at',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'published_at'    => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function coverUrl(): ?string
    {
        return \App\Support\FileUrl::resolve($this->cover_image, 'books/covers', 'uploads/books/covers');
    }

    public function bookFileUrl(): ?string
    {
        return \App\Support\FileUrl::resolve($this->book, 'books', 'books');
    }
}
