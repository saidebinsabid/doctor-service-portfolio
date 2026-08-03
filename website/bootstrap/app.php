<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        /* ভাষা নির্ধারণ সবার আগে — বাকি সব মিডলওয়্যার যেন সঠিক locale পায় */
        $middleware->web(prepend: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        /* নিরাপত্তা হেডার প্রতিটি রেসপন্সে */
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        /* লগইন না করা অবস্থায় অ্যাডমিন পেজে গেলে অ্যাডমিন লগইনে পাঠাও
           (ডিফল্ট 'login' route এখানে নেই, তাই আগে 500 হতো) */
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
