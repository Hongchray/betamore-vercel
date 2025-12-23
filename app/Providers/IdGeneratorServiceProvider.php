<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\IdGeneratorService;

class IdGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(IdGeneratorService::class, function ($app) {
            return new IdGeneratorService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
