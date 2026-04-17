<?php

namespace App\Providers;

use App\Services\AnalysisService;
use App\Services\BentFunctionService;
use App\Services\QuadraticApproximationService;
use App\Services\SboxService;
use App\Services\WalshHadamardService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WalshHadamardService::class);

        $this->app->singleton(BentFunctionService::class, function ($app) {
            return new BentFunctionService(
                $app->make(WalshHadamardService::class)
            );
        });

        $this->app->singleton(SboxService::class, function ($app) {
            return new SboxService(
                $app->make(BentFunctionService::class),
                $app->make(WalshHadamardService::class)
            );
        });

        $this->app->singleton(AnalysisService::class, function ($app) {
            return new AnalysisService(
                $app->make(BentFunctionService::class),
                $app->make(WalshHadamardService::class),
                $app->make(QuadraticApproximationService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
