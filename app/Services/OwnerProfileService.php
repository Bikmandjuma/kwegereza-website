<?php

namespace App\Services;

use App\Models\Owner;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

/**
 * Profile/Settings phase: genuinely absent for owners before this — the
 * only existing "profile update" code (TeacherVerificationController::
 * updateProfile) is an ADMIN editing a TEACHER's public bio during
 * verification, not an owner managing their own account. Student-side
 * privacy settings (PrivacyController) already existed but had no
 * owner-side equivalent at all.
 */
class OwnerProfileService
{
    use HandlesFileUploads;

    public function updateProfile(Owner $owner, array $data): Owner
    {
        $owner->update([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'phone'     => $data['phone'],
            'bio'       => $data['bio'] ?? null,
        ]);

        return $owner;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePassword(Owner $owner, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $owner->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => ['Ijambo banga risanzwe ntabwo ari ryo.'],
            ]);
        }

        $owner->update(['password' => Hash::make($newPassword)]);
    }

    /**
     * Originally hardcoded Storage::disk('public') directly — the same
     * ephemeral-disk mistake HandlesFileUploads exists specifically to
     * prevent everywhere else in the app. Fixed to go through that same
     * trait, so avatars respect FILESYSTEM_DISK exactly like every other
     * upload (Darsat audio, book PDFs, Inyandiko images) instead of being
     * the one upload path still guaranteed to vanish on Railway/any
     * ephemeral host regardless of how the rest of the app is configured.
     */
    public function updateAvatar(Owner $owner, UploadedFile $file): Owner
    {
        $this->deleteUploadedFile($owner->image, 'avatars');
        $filename = $this->storeUploadedFile($file, 'avatars');

        $owner->update(['image' => $filename]);

        return $owner;
    }
}
