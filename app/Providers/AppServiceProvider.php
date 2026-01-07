<?php

namespace App\Providers;

use App\Models\Grain;
use App\Models\Loan;
use App\Observers\GrainObserver;
use App\Observers\LoanObserver;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Loan::observe(LoanObserver::class);
        Grain::observe(GrainObserver::class);
        Scramble::configure()
            ->expose(
                ui: '/docs/v1/api',
                document: '/docs/v1/openapi.json',
            );
    }
}
