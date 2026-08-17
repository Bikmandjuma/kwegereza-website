<?php

namespace App\Traits;

use App\Models\Favorite;

trait Favoritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function isFavoritedBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->favorites()->where('user_id', $userId)->exists();
    }
}
