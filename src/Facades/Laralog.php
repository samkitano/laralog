<?php

namespace samkitano\Laralog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Laralog
 *
 * @method static list()
 * @method static dates()
 * @method static group()
 * @method static process($log)
 * @method static latest()
 */
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
