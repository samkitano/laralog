<?php

namespace samkitano\Laralog\Tests;

use Illuminate\Support\Facades\Config;
use samkitano\Laralog\LaralogServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $test_logs;

    protected $initial_config;

    protected function setUp()
    {
        parent::setUp();

        $test_logs = glob(__DIR__.'/../storage/logs/*.log');

        $this->test_logs = array_map('realpath', $test_logs);

        $this->initial_config = Config::get('laralog.path');

        Config::set('laralog.path', __DIR__.'/../storage/logs');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaralogServiceProvider::class
        ];
    }

    protected function resetConfig()
    {
        Config::set('laralog.path', $this->initial_config);
    }
}
