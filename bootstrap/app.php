<?php

use App\Support\Venatomar;
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
    ->withMiddleware(function (Middleware $middleware) {
        // The consent cookie is written and read by the browser script.
        $middleware->encryptCookies(except: ['pf_consent']);

        // Guests hitting /{locale}/my-account/dashboard go to the login form.
        $middleware->redirectGuestsTo(function (Request $request) {
            $locale = $request->segment(1);

            return route('account', ['locale' => Venatomar::isLocale($locale) ? $locale : config('venatomar.default_locale')]);
        });

        $middleware->priority([
            \App\Http\Middleware\SetLocale::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Never log form input (personal data) with exceptions.
        $exceptions->dontFlash(['document_number', 'bank_account', 'mm_number', 'birthday', 'monthly_income', 'email', 'phone']);
    })->create();
