<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, \App\Traits\Favoritable;

    protected $fillable = [
        'title', 'slug', 'description', 'image', 'location',
        'starts_at', 'ends_at', 'capacity', 'status', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * The stored value is always UTC (config('app.timezone')) — these
     * return it converted to the timezone admins actually think in, for
     * display and for pre-filling the edit form's datetime-local input.
     */
    public function startsAtLocal(): ?\Illuminate\Support\Carbon
    {
        return $this->starts_at?->copy()->setTimezone('Africa/Kigali');
    }

    public function endsAtLocal(): ?\Illuminate\Support\Carbon
    {
        return $this->ends_at?->copy()->setTimezone('Africa/Kigali');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now());
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function imageUrl(): ?string
    {
        return \App\Support\FileUrl::resolve($this->image, 'events');
    }

    public function isFull(): bool
    {
        return $this->capacity !== null && $this->registrations()->count() >= $this->capacity;
    }

    public function isRegisteredBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->registrations()->where('user_id', $userId)->exists();
    }

    public function spotsLeft(): ?int
    {
        if ($this->capacity === null) {
            return null;
        }

        return max(0, $this->capacity - $this->registrations()->count());
    }
}
