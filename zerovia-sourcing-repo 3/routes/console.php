<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Example: nightly ESG score sync from ZEROvia Hub
// Schedule::command('zerovia:sync-esg-scores')->dailyAt('02:00');
