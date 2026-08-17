<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'users'        => ['view', 'create', 'update', 'delete'],
            'roles'        => ['view', 'create', 'update', 'delete'],
            'permissions'  => ['view', 'create', 'update', 'delete'],
            'darsat'       => ['view', 'create', 'update', 'delete'],
            'students'     => ['view', 'manage'],
            'support'      => ['view', 'manage'],
            'account_deletion' => ['manage'],
            'backups'      => ['view', 'create', 'delete'],
            'system_monitoring' => ['view'],
            'group_chat'   => ['leaders', 'moderate'],
            'books'        => ['view', 'create', 'update', 'delete'],
            'inyandiko'    => ['view', 'create', 'update', 'delete'],
            'amatangazo'   => ['view', 'create', 'update', 'delete'],
            'courses'      => ['view', 'create', 'update', 'delete'],
            'quizzes'      => ['view', 'create', 'update', 'delete'],
            'certificates' => ['view', 'issue'],
            'gamification' => ['view', 'manage'],
            'feature_flags' => ['manage'],
            'events'       => ['view', 'create', 'update', 'delete'],
            'comments'     => ['moderate'],
            'teacher_verification' => ['manage'],
            'chat'         => ['view', 'reply', 'moderate'],
            'live_class'   => ['create', 'manage', 'moderate', 'speak_permission'],
            'analytics'    => ['view'],
            'audit_logs'   => ['view'],
            'settings'     => ['manage'],
        ];

        $labels = [
            'view' => 'View', 'create' => 'Create', 'update' => 'Update', 'delete' => 'Delete',
            'reply' => 'Reply', 'moderate' => 'Moderate', 'manage' => 'Manage',
            'speak_permission' => 'Grant Speak Permission',
        ];

        $allSlugs = [];

        foreach ($groups as $group => $actions) {
            foreach ($actions as $action) {
                $slug = "{$group}.{$action}";
                $allSlugs[] = $slug;

                Permission::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name'  => ucfirst(str_replace('_', ' ', $group)) . ' — ' . ($labels[$action] ?? ucfirst($action)),
                        'group' => $group,
                    ]
                );
            }
        }

        // ---- Roles ----

        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'description' => 'Full, unrestricted control of the platform.', 'is_super' => true]
        );

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Manages most platform features.', 'is_super' => false]
        );
        $admin->permissions()->sync(
            Permission::whereIn('slug', array_diff($allSlugs, [
                'roles.delete', 'permissions.delete', 'settings.manage',
            ]))->pluck('id')
        );

        $leader = Role::firstOrCreate(
            ['slug' => 'islamic-leader'],
            ['name' => 'Islamic Leader', 'description' => 'Responds to guest/leader conversations and teaches.', 'is_super' => false]
        );
        $leader->permissions()->sync(
            Permission::whereIn('slug', [
                'chat.view', 'chat.reply',
                'darsat.view', 'darsat.create', 'darsat.update',
                'live_class.create', 'live_class.manage', 'live_class.speak_permission',
                'group_chat.leaders',
            ])->pluck('id')
        );

        $teacher = Role::firstOrCreate(
            ['slug' => 'teacher'],
            ['name' => 'Teacher', 'description' => 'Manages assigned learning content and classes.', 'is_super' => false]
        );
        $teacher->permissions()->sync(
            Permission::whereIn('slug', [
                'darsat.view', 'darsat.create', 'darsat.update',
                'courses.view', 'courses.create', 'courses.update',
                'quizzes.view', 'quizzes.create', 'quizzes.update',
                'live_class.create', 'live_class.manage',
            ])->pluck('id')
        );

        $moderator = Role::firstOrCreate(
            ['slug' => 'moderator'],
            ['name' => 'Moderator', 'description' => 'Moderates content and conversations.', 'is_super' => false]
        );
        $moderator->permissions()->sync(
            Permission::whereIn('slug', [
                'chat.view', 'chat.moderate', 'live_class.moderate', 'comments.moderate',
            ])->pluck('id')
        );

        // Give the very first registered owner Super Admin, so there is always
        // at least one account able to manage roles/permissions after this
        // seeder runs on a fresh install.
        $firstOwner = Owner::orderBy('id')->first();

        if ($firstOwner && !$firstOwner->roles()->where('roles.id', $superAdmin->id)->exists()) {
            $firstOwner->roles()->syncWithoutDetaching([$superAdmin->id]);
        }

        // SAFETY NET: routes are about to start enforcing these permissions.
        // Any pre-existing owner account with zero roles would otherwise be
        // locked out of everything the moment that lands. Default every
        // roleless owner to Admin (not Super Admin) so nobody is silently
        // shut out — you can then reassign real roles per person from the
        // Roles page whenever you're ready.
        Owner::whereDoesntHave('roles')->get()->each(function ($owner) use ($admin) {
            $owner->roles()->syncWithoutDetaching([$admin->id]);
        });
    }
}
