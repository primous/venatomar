<?php

namespace App\Providers;

use App\Http\Middleware\SetLocale;
use App\Support\Country;
use App\Mail\LeadConfirmation;
use App\Mail\LeadReceivedInternal;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Fallback when resolved outside a localized route (console, mail jobs).
        $this->app->bind(Country::class, fn () => Country::current());
        $this->app->singleton(\App\Services\WebPushService::class);
    }

    public function boot(): void
    {
        // Re-applied on every Livewire update request (/livewire/update has no locale prefix).
        Livewire::addPersistentMiddleware([SetLocale::class]);

        // Delivery tracking on the Lead (used by venatomar:retry-mails).
        Event::listen(MessageSent::class, function (MessageSent $event) {
            $column = match ($event->data['__laravel_mailable'] ?? null) {
                LeadReceivedInternal::class => 'internal_mail_sent_at',
                LeadConfirmation::class => 'confirmation_mail_sent_at',
                default => null,
            };
            $lead = $event->data['lead'] ?? null;
            if ($column && $lead instanceof \App\Models\Lead) {
                $lead->forceFill([$column => now()])->saveQuietly();
            }
        });
    }
}
