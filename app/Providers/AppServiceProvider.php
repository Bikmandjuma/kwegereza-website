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
        // Confirmed missing during the Audit Logs phase: Course and Quiz
        // are exactly the same kind of admin-managed content as
        // Amatangazo/Inyandiko/Book/DarsatTable above (all audited), but
        // were never added here — meaning content management's two
        // largest features had zero audit trail while their siblings did.
        \App\Models\Course::observe(\App\Observers\AuditObserver::class);
        \App\Models\Quiz::observe(\App\Observers\AuditObserver::class);
    }
}
