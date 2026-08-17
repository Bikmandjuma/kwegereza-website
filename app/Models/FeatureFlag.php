<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'label', 'description', 'is_enabled'];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('feature_flags.all'));
        static::deleted(fn () => Cache::forget('feature_flags.all'));
    }

    /**
     * FeatureFlag::enabled('guest_chat')
     *
     * Defaults to true for any key that doesn't exist yet in the DB, so a
     * feature never silently disappears just because its flag row hasn't
     * been seeded — a flag has to be explicitly turned off to hide something.
     */
    public static function enabled(string $key): bool
    {
        $flags = Cache::remember('feature_flags.all', 3600, function () {
            return static::pluck('is_enabled', 'key');
        });

        return $flags[$key] ?? true;
    }
}
