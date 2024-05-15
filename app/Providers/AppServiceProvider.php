<?php

namespace App\Providers;

use App\Telegram\FSM\StateManager;
use DefStudio\Telegraph\Storage\CacheStorageDriver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StateManager::class, function ($app) {

            $config = $app->make('config')->get('telegraph.storage.stores.cache', []);

            return new StateManager($this->app->make(CacheStorageDriver::class, ['itemClass' => StateManager::class, 'itemKey' => 'tgph', 'configuration' => $config]));
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
