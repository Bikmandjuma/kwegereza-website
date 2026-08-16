<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression test for the live bug found via the misplaced-namespace
 * sweep: these two routes have been calling GuestController::events()/
 * eventShow(), methods that only existed in an unreachable stray file —
 * meaning every real visit to these pages threw a fatal error.
 */
class GuestEventsRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_events_listing_page_no_longer_fatal_errors(): void
    {
        Event::create([
            'title' => 'Iterambere ry\'Ubwuzuzanye', 'slug' => 'iterambere',
            'status' => 'published', 'starts_at' => now()->addDays(3),
        ]);

        $response = $this->get('/ibikorwa');

        $response->assertOk();
        $response->assertSee('Iterambere');
    }

    public function test_a_single_event_page_no_longer_fatal_errors(): void
    {
        Event::create([
            'title' => 'Isengesho rusange', 'slug' => 'isengesho-rusange',
            'status' => 'published', 'starts_at' => now()->addDays(1),
        ]);

        $response = $this->get('/ibikorwa/isengesho-rusange');

        $response->assertOk();
        $response->assertSee('Isengesho rusange');
    }
}
