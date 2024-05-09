<?php

namespace App\Providers;

use App\Telegram\FSM\CarFSM;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CarFSM::class, function ($app) {
            return new CarFSM($app->make('telegraph.storage'));
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
