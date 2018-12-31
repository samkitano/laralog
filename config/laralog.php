<?php

/*
|--------------------------------------------------------------------------
| Laralog - A Log manager for Laravel 5.7
|--------------------------------------------------------------------------
|
| Here you can change this utility default settings.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Logs Path
    |--------------------------------------------------------------------------
    |
    | Where do you usually keep your logs?
    | Default - the Laravel pre-defined
    | directory.
    |
    */

    'path' => storage_path('logs'),

    /*
    |--------------------------------------------------------------------------
    | Master Logs Archive Name
    |--------------------------------------------------------------------------
    |
    | The name for the compressed logs archive.
    | Individual log files can be compressed
    | onto this master archive, if needed.
    |
    */

    'main_archive' => 'laravel-logs',
];
