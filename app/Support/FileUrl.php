<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolves the public URL for a stored upload, checking the configured
 * filesystem disk first. Falls back to the old public_path() location only
 * if the file still physically exists there — for uploads that happened
 * before this patch. Once a Railway restart wipes those, this fallback
 * naturally stops finding them; there's no way around that for files that
 * were never actually persisted in the first place. New uploads never hit
 * this fallback at all.
 */
class FileUrl
{
    public static function resolve(?string $filename, string $folder, ?string $legacyPublicFolder = null): ?string
    {
        if (!$filename) {
            return null;
        }

        $disk = config('filesystems.default', 'public');
        $path = trim($folder, '/') . '/' . $filename;

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        if ($legacyPublicFolder && file_exists(public_path(trim($legacyPublicFolder, '/') . '/' . $filename))) {
            return asset(trim($legacyPublicFolder, '/') . '/' . $filename);
        }

        return null;
    }
}
