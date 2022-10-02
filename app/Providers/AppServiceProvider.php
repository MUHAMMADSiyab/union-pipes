<?php

namespace App\Providers;

use App\Models\Distribution;
use App\Models\Purchase;
use App\Observers\DistributionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Distribution::observe(DistributionObserver::class);
    }
}
