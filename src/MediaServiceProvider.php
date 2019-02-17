<?php

namespace Khaleghi\Media;

use Illuminate\Support\ServiceProvider;

class MediaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ .'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'media');
        $this->mergeConfigFrom(
            __DIR__.'/config/media.php', 'media'
        );

        $this->publishes([
            __DIR__.'/config/media.php' => config_path('media.php'),
            __DIR__.'/database/migrations/' => database_path('migrations'),
        ]);

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
