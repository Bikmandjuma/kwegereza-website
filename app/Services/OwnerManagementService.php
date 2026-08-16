<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\Role;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\UploadedFile;

/**
 * Users/Admin phase: extracted from AdminController's Owner CRUD, fixing
 * the severe model-confusion bug found in the process — edit(), update(),
 * and destroy() all fetched User::findOrFail() (the STUDENT model)
 * instead of Owner::findOrFail(), despite managing staff/leader/admin
 * accounts. Any admin editing or deleting a staff member whose numeric ID
 * happened to collide with an existing student's ID would silently act on
 * that unrelated student instead, leaving the actual owner record
 * untouched. See AdminUserManagementModelConfusionTest for the proof.
 */
class OwnerManagementService
{
    use HandlesFileUploads;

    public function paginate(int $perPage = 15, ?string $title = null, ?string $search = null)
    {
        return Owner::with('roles')
            ->when($title, fn ($q) => $q->where('title', $title))
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('firstname', 'like', "%{$search}%")
                   ->orWhere('lastname', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Owner
    {
        return Owner::with('roles')->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null): Owner
    {
        $imageName = $image ? $this->storeUploadedFile($image, 'images/users') : 'user.png';

        $owner = Owner::create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'gender'    => $data['gender'],
            'phone'     => $data['phone'],
            'image'     => $imageName,
            'dob'       => $data['dob'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'title'     => $data['title'],
            'password'  => bcrypt($data['password']),
        ]);

        if (! empty($data['role_slugs'])) {
            $this->syncRoles($owner, $data['role_slugs']);
        }

        return $owner;
    }

    public function update(Owner $owner, array $data, ?UploadedFile $image = null): Owner
    {
        $imageName = $owner->image;
        if ($image) {
            if ($owner->image && $owner->image !== 'user.png') {
                $this->deleteUploadedFile($owner->image, 'images/users');
            }
            $imageName = $this->storeUploadedFile($image, 'images/users');
        }

        $owner->update([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'gender'    => $data['gender'],
            'phone'     => $data['phone'],
            'image'     => $imageName,
            'dob'       => $data['dob'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'title'     => $data['title'],
        ]);

        if (! empty($data['password'])) {
            $owner->update(['password' => bcrypt($data['password'])]);
        }

        if (isset($data['role_slugs'])) {
            $this->syncRoles($owner, $data['role_slugs']);
        }

        return $owner;
    }

    public function delete(Owner $owner): void
    {
        if ($owner->image && $owner->image !== 'user.png') {
            $this->deleteUploadedFile($owner->image, 'images/users');
        }

        $owner->delete();
    }

    private function syncRoles(Owner $owner, array $roleSlugs): void
    {
        $roleIds = Role::whereIn('slug', $roleSlugs)->pluck('id');
        $owner->roles()->sync($roleIds);
    }
}
