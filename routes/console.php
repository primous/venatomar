<?php

use App\Models\MagicLoginToken;
use Illuminate\Support\Facades\Schedule;

/*
| Hostinger shared hosting: one cron entry every minute
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
| The scheduler then drains the database queue (e-mails) and stops when empty.
*/
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground();

Schedule::command('venatomar:invite-reviews')->dailyAt('10:00')->withoutOverlapping();

Schedule::call(fn () => MagicLoginToken::where('expires_at', '<', now()->subDay())->delete())
    ->daily()
    ->name('prune-magic-login-tokens');
