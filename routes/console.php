<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Run PageSpeed analysis for all sites with an active schedule.
// The command itself filters by schedule frequency (daily/weekly/monthly).
Schedule::command('pagespeed:run-scheduled')->hourly()->withoutOverlapping();
