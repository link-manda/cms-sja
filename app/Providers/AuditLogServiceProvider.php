<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AuditLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Auth Events
        Event::listen(Login::class, function ($event) {
            Log::channel('audit')->info('Login successful', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Logout::class, function ($event) {
            Log::channel('audit')->info('Logout', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'ip' => request()->ip(),
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Failed::class, function ($event) {
            Log::channel('audit')->warning('Login failed', [
                'email' => $event->credentials['email'] ?? 'unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'guard' => $event->guard,
            ]);
        });

        Event::listen(Lockout::class, function ($event) {
            Log::channel('audit')->error('Account locked out', [
                'email' => $event->request->input('email'),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
