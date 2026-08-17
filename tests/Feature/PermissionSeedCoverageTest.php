<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Automated version of the manual sweep that found 5 permission slugs
 * (students.*, support.*, account_deletion.manage, backups.*,
 * system_monitoring.view) referenced by controller middleware but never
 * present in RolePermissionSeeder — meaning no non-super-admin owner could
 * ever pass those checks, regardless of role. Scans every controller for
 * `permission:x.y` middleware strings and asserts each one exists as a real
 * Permission row after seeding, so this bug class can't silently reappear
 * when a new controller is added.
 */
class PermissionSeedCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_permission_referenced_by_a_controller_is_actually_seeded(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $referenced = [];
        $controllerFiles = array_merge(
            glob(app_path('Http/Controllers/Web/*.php')),
            glob(app_path('Http/Controllers/*.php')),
        );

        foreach ($controllerFiles as $file) {
            $contents = file_get_contents($file);
            if (preg_match_all('/permission:([a-z_]+\.[a-z_]+)/', $contents, $matches)) {
                foreach ($matches[1] as $slug) {
                    $referenced[$slug] = true;
                }
            }
        }

        $this->assertNotEmpty($referenced, 'Sanity check: the scan itself should find some permission: references.');

        $seededSlugs = \App\Models\Permission::pluck('slug')->all();
        $missing = array_diff(array_keys($referenced), $seededSlugs);

        $this->assertEmpty(
            $missing,
            'These permission slugs are checked by a controller but never seeded, meaning no non-super-admin owner can ever pass: '.implode(', ', $missing)
        );
    }
}
