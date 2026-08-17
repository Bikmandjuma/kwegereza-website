<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Replaces the app's old pattern of $file->move(public_path('...'), $name),
 * which writes straight to the app server's local disk. On Railway (and any
 * other platform without a persistent volume), everything written that way
 * disappears on the next redeploy or restart.
 *
 * This trait writes through Laravel's Storage facade instead, using
 * whichever disk FILESYSTEM_DISK is set to:
 *   - 'public' locally (storage/app/public, symlinked to public/storage) —
 *     still local disk, but at least consistent and swappable in one place
 *   - 's3' in production — an actual S3-compatible bucket (Railway's own
 *     object storage add-on, Cloudflare R2, DigitalOcean Spaces, AWS S3,
 *     anything speaking the S3 API works via the 'endpoint' env var)
 *
 * Nothing else in the calling controller needs to know which one is active.
 */
trait HandlesFileUploads
{
    protected function uploadDisk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Store an uploaded file under the given folder and return just the
     * generated filename (not the full path) — matches what the existing
     * DB columns already store, so no column changes are needed.
     */
    protected function storeUploadedFile(?UploadedFile $file, string $folder): ?string
    {
        if (!$file) {
            return null;
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = time() . '_' . Str::slug($originalName) . '.' . $file->getClientOriginalExtension();

        $file->storeAs($folder, $filename, $this->uploadDisk());

        return $filename;
    }

    /**
     * Delete a previously-stored file, if it exists on the active disk.
     */
    protected function deleteUploadedFile(?string $filename, string $folder): void
    {
        if (!$filename) {
            return;
        }

        $path = trim($folder, '/') . '/' . $filename;

        if (Storage::disk($this->uploadDisk())->exists($path)) {
            Storage::disk($this->uploadDisk())->delete($path);
        }
    }
}
