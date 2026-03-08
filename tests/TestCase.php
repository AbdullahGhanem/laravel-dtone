<?php

namespace Ghanem\Dtone\Tests;

use Ghanem\Dtone\DtoneServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DtoneServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Dtone' => \Ghanem\Dtone\Facades\Dtone::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('dtone.key', 'test-key');
        $app['config']->set('dtone.secret', 'test-secret');
        $app['config']->set('dtone.test_key', 'test-sandbox-key');
        $app['config']->set('dtone.test_secret', 'test-sandbox-secret');
        $app['config']->set('dtone.is_production', false);
    }
}
