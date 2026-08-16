<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registered under /api with auth:sanctum (not the default 'web'
        // session guard) since React authenticates via Sanctum bearer
        // tokens, not cookies — matching every other owner API route.
        Broadcast::routes(['middleware' => ['auth:sanctum'], 'prefix' => 'api']);

        require base_path('routes/channels.php');
    }
}
