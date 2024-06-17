<?php
namespace Angstrom\MoMo;

use Illuminate\Support\ServiceProvider;

class MoMoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/momo.php', 'momo');

        $this->app->singleton(MoMoClient::class, function ($app) {
            return new MoMoClient();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/momo.php' => config_path('momo.php'),
        ]);
    }
}
