<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        if (App::environment() === "production") {
            URL::forceScheme("https");
        }

        // Audit logging (Section 48). One line per model — nothing else
        // needed to add auditing to a new model later.
        \App\Models\Role::observe(\App\Observers\AuditObserver::class);
        \App\Models\Permission::observe(\App\Observers\AuditObserver::class);
        \App\Models\Owner::observe(\App\Observers\AuditObserver::class);
        \App\Models\User::observe(\App\Observers\AuditObserver::class);
        \App\Models\Amatangazo::observe(\App\Observers\AuditObserver::class);
        \App\Models\Inyandiko::observe(\App\Observers\AuditObserver::class);
        \App\Models\Book::observe(\App\Observers\AuditObserver::class);
        \App\Models\DarsatTable::observe(\App\Observers\AuditObserver::class);
    }
}
