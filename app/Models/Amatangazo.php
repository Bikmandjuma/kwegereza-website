<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amatangazo extends Model
{
    use HasFactory;

    protected $table = 'amatangazos';

    protected $fillable = [
        'title',
        'description',
        'presenter',
        'image',
        'status',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeStatus($query, $status)
    {
        if (!$status || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function creator()
    {
        return $this->belongsTo(Owner::class, 'created_by');
    }

    public function imageUrl(): string
    {
        return \App\Support\FileUrl::resolve($this->image, 'amatangazo', 'uploads/amatangazo')
            ?? asset('Guest/images/logo.png');
    }
}
