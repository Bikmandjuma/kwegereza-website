<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DarsatTable extends Model{
    use HasFactory, \App\Traits\Favoritable;

    protected $table = 'darsat_tables';

    protected $fillable = [
        'title',
        'teachers',
        'type',
        'description',
        'audio',
        'thumbnail',
        'status',
        'plays',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Teacher relationship
     */
    public function teacher()
    {
        return $this->belongsTo(Owner::class, 'teachers');
    }

    public function progress()
    {
        return $this->hasMany(DarsatProgress::class, 'darsat_id');
    }

    public function audioUrl(): ?string
    {
        if (!$this->audio) {
            return null;
        }

        // Rows created before this patch stored the full "audio/filename.ext"
        // path returned by Storage::storeAs(). Rows created after this patch
        // store just the filename, like every other model in this app. Handle
        // both without needing a data migration.
        if (str_contains($this->audio, '/')) {
            return \App\Support\FileUrl::resolve(basename($this->audio), dirname($this->audio));
        }

        return \App\Support\FileUrl::resolve($this->audio, 'audio', 'uploads/audio');
    }

    public function thumbnailUrl(): ?string
    {
        return \App\Support\FileUrl::resolve($this->thumbnail, 'darsat/thumbnails', 'uploads/darsat/thumbnails');
    }
}