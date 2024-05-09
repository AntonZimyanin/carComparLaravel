<?php

namespace App\Providers;

use App\Telegram\FSM\State;
use DefStudio\Telegraph\Storage\CacheStorageDriver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(State::class, function ($app) {

            $config = $app->make('config')->get('telegraph.storage.stores.cache', []);

            return new State($this->app->make(CacheStorageDriver::class, ['itemClass' => State::class, 'itemKey' => 'tgph', 'configuration' => $config]));
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
