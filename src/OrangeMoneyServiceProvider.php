<?php

namespace OrangeMoney;

use OrangeMoney\Services\OrangeMoney;
use Illuminate\Support\ServiceProvider;

class OrangeMoneyService extends ServiceProvider
{
    public function registerFacades()
    {
        $this->app->singleton(OrangeMoney::class, function () {
            return new OrangeMoney();
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/orangemoney.php', 'orangemoney');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/orangemoney.php' => config_path('orangemoney.php')
        ]);
    }
}