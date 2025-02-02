<?php

namespace Angstrom\MoMo\Tests;

use Angstrom\MoMo\MoMoServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MoMoServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('momo.api_key', 'test-api-key');
        $app['config']->set('momo.api_user', 'test-api-user');
        $app['config']->set('momo.api_base_url', 'https://sandbox.momodeveloper.mtn.com');
        $app['config']->set('momo.environment', 'sandbox');
    }
}
