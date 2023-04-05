<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\ReturnedSoldItem;
use App\Observers\PaymentObserver;
use App\Observers\ReturnedSoldItemObserver;
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
    }
}
