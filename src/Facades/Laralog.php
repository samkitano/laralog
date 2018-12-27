<?php

namespace samkitano\Laralog\Facades;

use Illuminate\Support\Facades\Facade;

class Laralog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'Laralog';
    }
}
