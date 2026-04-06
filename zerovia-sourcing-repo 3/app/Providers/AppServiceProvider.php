<?php

namespace App\Providers;

use App\Services\NogaService;
use App\Services\RfqDispatchService;
use App\Services\RfqGeneratorService;
use App\Services\SourcingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SourcingService::class);
        $this->app->singleton(RfqGeneratorService::class);
        $this->app->singleton(RfqDispatchService::class);
        $this->app->singleton(NogaService::class);
    }

    public function boot(): void
    {
        //
    }
}
