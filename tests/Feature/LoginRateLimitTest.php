<?php

namespace Tests\Feature;

use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Regression test for a real, confirmed security gap: the owner login
 * endpoint (POST /api/owner/auth/login) had no rate limiting at all —
 * checked the route definition, controller, and global middleware before
 * concluding this, rather than assuming Laravel's defaults covered it.
 * An attacker could attempt unlimited password guesses against any known
 * owner email with no throttling whatsoever.
 */
class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_login_is_rate_limited_after_repeated_failed_attempts(): void
    {
        Owner::create([
            'firstname' => 'Test', 'lastname' => 'Owner', 'gender' => 'male',
            'image' => 'user.png', 'dob' => '1990-01-01', 'is_verified' => 0,
            'email' => 'ratelimit@test.com', 'phone' => '0700000180',
            'password' => Hash::make('correct-password'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/owner/auth/login', [
                'email' => 'ratelimit@test.com', 'password' => 'wrong-password',
            ]);
            $response->assertStatus(422); // normal "wrong credentials" (ValidationException), not yet rate limited
        }

        $response = $this->postJson('/api/owner/auth/login', [
            'email' => 'ratelimit@test.com', 'password' => 'correct-password',
        ]);

        $response->assertStatus(429);
    }
}
