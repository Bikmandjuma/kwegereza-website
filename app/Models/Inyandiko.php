<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inyandiko extends Model
{
    use HasFactory;

    protected $table = 'inyandikos';

    protected $fillable = [
        'title', 'slug', 'category', 'author', 'summary', 'content',
        'image', 'file', 'status', 'created_by', 'updated_by', 'published_at',
        'comments_enabled',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'comments_enabled' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function imageUrl(): string
    {
        return \App\Support\FileUrl::resolve($this->image, 'inyandiko', 'uploads/inyandiko')
            ?? asset('Guest/images/logo.png');
    }

    public function fileUrl(): ?string
    {
        return \App\Support\FileUrl::resolve($this->file, 'inyandiko/files', 'uploads/inyandiko/files');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
