<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Production;
use App\Models\ReturnedSoldItem;
use App\Models\SoldItem;
use App\Observers\PaymentObserver;
use App\Observers\ProductionObserver;
use App\Observers\ReturnedSoldItemObserver;
use App\Observers\SoldItemObserver;
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
        Payment::observe(PaymentObserver::class);
        ReturnedSoldItem::observe(ReturnedSoldItemObserver::class);
        Production::observe(ProductionObserver::class);
        SoldItem::observe(SoldItemObserver::class);
    }
}
