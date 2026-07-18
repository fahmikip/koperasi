<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(Login::class, fn (Login $event) => activity()->causedBy($event->user)->withProperties(['ip' => request()->ip()])->log('login'));
        Event::listen(Logout::class, fn (Logout $event) => $event->user && activity()->causedBy($event->user)->withProperties(['ip' => request()->ip()])->log('logout'));
    }
}
