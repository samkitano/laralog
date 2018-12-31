[![Build Status](https://travis-ci.org/samkitano/laralog.svg?branch=master)](https://travis-ci.org/samkitano/laralog)

# A log viewer for Laravel

Laralog breaks down log files created by [Monolog](https://github.com/Seldaek/monolog) in your Laravel app into
an object, to ease up the reading of those files.

## REQUIREMENTS
- PHP >= 7.1.3
- Laravel 5.7.*
- At this time, logging channel should be configured to 'stack' and 'daily' at ```config\logging.php``` (Laravel defaults)

## INSTALLATION
```bash
composer require samkitano/laralog
```

## CONFIGURATION
Publish configuration (optional):

```bash
php artisan vendor:publish --tag=laralog
```

The configuration file contains detailed descriptions of all and each entry.

## USAGE
Results in these examples are based on the included files for testing at ```laralog\storage\logs```

### To fetch a List (array) with the names of the existing log files:
```php
$list = Laralog::list();

dump($list);
```
Result:
```bash
array:6 [
  0 => "\packages\laralog\storage\logs\laravel-2017-06-10.log"
  1 => "\packages\laralog\storage\logs\laravel-2017-12-02.log"
  2 => "\packages\laralog\storage\logs\laravel-2018-11-27.log"
  3 => "\packages\laralog\storage\logs\laravel-2018-12-07.log"
  4 => "\packages\laralog\storage\logs\laravel-2018-12-17.log"
  5 => "\packages\laralog\storage\logs\laravel-2018-12-27.log"
]
```
### To fetch a List (array) with the dates of the existing log files:
```php
$dates = Laralog::dates();

dump($dates);
```
Result:
```bash
array:6 [
  0 => "2017-06-10"
  1 => "2017-12-02"
  2 => "2018-11-27"
  3 => "2018-12-07"
  4 => "2018-12-17"
  5 => "2018-12-27"
]
```

### To fetch a List (array) with the grouped dates of the existing log files:
```php
$groupped = Laralog::group();

dump($groupped);
```
Result:
```bash
array:2 [
  2017 => array:2 [
    "Jun" => array:1 [
      0 => "10"
    ]
    "Dec" => array:1 [
      0 => "02"
    ]
  ]
  2018 => array:2 [
    "Nov" => array:1 [
      0 => "27"
    ]
    "Dec" => array:3 [
      0 => "07"
      1 => "17"
      2 => "27"
    ]
  ]
]
```
### To process a log:
Use ```Laralog::latest()``` to process the most recent log, or the
```Laralog::process()``` command which accepts as an argument:

- [null]. If no argument is passed, the most recent log will be processed;
- [string] a name. i.e. 'laravel-2017-06-10.log';
- [string] a date.  i.e. '2017-Jun-10', or '2017-6-10';
- [object] a [Carbon](https://carbon.nesbot.com/) object. i.e. Carbon::create(2017, 6, 10);

```php
$log = Laralog::process('2017-Jun-10');

dump($log);
```
Result (some output was shortened for readability):
```bash
"2017-06-10" => array:4 [
    "name" => "laravel-2017-06-10.log"
    "path" => "F:\packages\laralog\storage\logs\laravel-2017-06-10.log"
    "length" => 1
    "entries" => array:1 [
      0 => array:6 [
        "raw_header" => "[2018-12-27 12:29:27] local.ERROR: Database (/local_dev/www/sam/database/sk.sqlite) does not exist. ...)"
        "datetime" => "2018-12-27 12:29:27"
        "env" => "local"
        "level" => "error"
        "error" => "Illuminate\\Database\\QueryException(code: 0): Database (/local_dev/www/sam/database/sk.sqlite) does not exist. ..."
        "stack" => """
          \n
          [stacktrace]\n
          #0 F:\\www\\sam\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Connection.php(624): Illuminate\\Database\\Connection->runQueryCallback('select * from \"...', Array, Object(Closure))\n
          #1 F:\\www\\sam\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Connection.php(333): Illuminate\\Database\\Connection->run('select * from \"...', Array, Object(Closure))\n
          #2 F:\\www\\sam\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Query\\Builder.php(2082): Illuminate\\Database\\Connection->select('select * from \"...', Array, true)\n
          ...
          ...
          #58 {main}\n
          "} \n
          """
      ]
    ]
  ]
]

```
## TODO
- Allow to fetch logs other than those in the local filesystem
- Allow other logging config settings
- A better abstraction for the breakdown process

## LICENSE
This package is open-source software, licensed under the [MIT license](https://opensource.org/licenses/MIT)
