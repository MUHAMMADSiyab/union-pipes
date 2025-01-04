<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Production;
use App\Models\Stock;
use App\Models\ReturnedSoldItem;
use App\Models\Sell;
use App\Models\SoldItem;
use App\Observers\PaymentObserver;
use App\Observers\ProductionObserver;
use App\Observers\StockObserver;
use App\Observers\ReturnedSoldItemObserver;
use App\Observers\SellObserver;
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
        Stock::observe(StockObserver::class);
        Production::observe(ProductionObserver::class);
        // Sell::observe(SellObserver::class);
        // SoldItem::observe(SoldItemObserver::class);
    }
}
