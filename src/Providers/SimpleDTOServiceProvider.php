<?php

namespace Tsarturi\SimpleDTO\Providers;

use Illuminate\Support\ServiceProvider;
use Tsarturi\SimpleDTO\Console\Commands\MakeDTOCommand;

class SimpleDTOServiceProvider extends ServiceProvider
{
    /**
     * @return void
     *
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(MakeDTOCommand::class);
        }

        $this->publishes(
            [
                __DIR__.'/../../config/simpledto.php' => base_path('config/simpledto.php'),
            ],
            'config'
        );
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/simpledto.php', 'simpledto');
    }
}
