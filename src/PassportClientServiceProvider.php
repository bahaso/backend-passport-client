<?php

namespace EdwinFadilah\PassportClient;

use EdwinFadilah\PassportClient\Exceptions\ExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as Handler;
use Illuminate\Support\ServiceProvider;

class PassportClientServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Do not forget to import them before using!
         */
        $this->app->bind(
            Handler::class,
            ExceptionHandler::class
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
