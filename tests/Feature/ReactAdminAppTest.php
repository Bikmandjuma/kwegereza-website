<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReactAdminAppTest extends TestCase
{
    public function test_the_admin_route_serves_the_react_shell_with_the_root_mount_div(): void
    {
        $response = $this->get('/admin');

        $response->assertOk();
        $response->assertSee('<div id="root">', false);
    }

    public function test_any_sub_path_under_admin_serves_the_same_shell_for_client_side_routing(): void
    {
        foreach (['/admin/dashboard', '/admin/courses', '/admin/roles', '/admin/some/deeply/nested/path'] as $path) {
            $response = $this->get($path);
            $response->assertOk();
            $response->assertSee('<div id="root">', false);
        }
    }

    public function test_the_admin_catchall_does_not_shadow_the_api_routes(): void
    {
        $response = $this->postJson('/api/owner/auth/login', ['email' => 'nope@test.com', 'password' => 'wrong']);

        $response->assertHeader('content-type', 'application/json');
        $this->assertStringNotContainsString('<div id="root">', $response->getContent());
    }

    public function test_the_admin_catchall_does_not_shadow_the_legacy_owner_blade_panel(): void
    {
        // Named 'owner.login' but the actual URL is /login, not
        // /owner/login — checked the real route before assuming.
        $response = $this->get('/login');

        $response->assertOk();
        $this->assertStringNotContainsString('<div id="root">', $response->getContent());
    }

    public function test_the_admin_catchall_does_not_shadow_student_routes(): void
    {
        $response = $this->get('/student/login');

        $response->assertOk();
        $this->assertStringNotContainsString('<div id="root">', $response->getContent());
    }
}
