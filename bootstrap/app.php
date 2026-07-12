<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle PostTooLargeException gracefully
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Total file size is too large.'], 413);
            }

            if ($request->is('manage/projects*')) {
                return back()->withErrors([
                    'gallery_images' => 'Total upload size is too large. Each gallery photo may not be greater than 4 MB, main photo may not be greater than 2 MB, and gallery may contain up to 10 photos.',
                ]);
            }

            return response()->view('errors.413', [], 413);
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
