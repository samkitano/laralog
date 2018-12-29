<?php

if (! defined('LL_LOG_EXTENSION')) {
    define('LL_LOG_EXTENSION', '.log');
}

if (! defined('LL_LOG_SEARCH_PATTERN')) {
    define('LL_LOG_SEARCH_PATTERN', '*.log');
}

if (! defined('LL_LOG_DATE_PATTERN')) {
    define('LL_LOG_DATE_PATTERN', '[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]');
}

if (! defined('LL_DATE_PATTERN')) {
    define('LL_DATE_PATTERN', '\d{4}(-\d{2}){2}');
}

if (! defined('LL_TIME_PATTERN')) {
    define('LL_TIME_PATTERN', '\d{2}(:\d{2}){2}');
}

if (! defined('LL_LOG_PREFIX')) {
    define('LL_LOG_PREFIX', 'laravel-');
}

if (! function_exists('maxDate')) {
    function maxDate(array $dates)
    {
        $timestamp = max(array_map('strtotime', $dates));

        return date('Y-m-j', $timestamp);
    }
}

if (! function_exists('string_between')) {
    /**
     * http://stackoverflow.com/questions/5696412/get-substring-between-two-strings-php
     *
     * @param string $string Haystack
     * @param string $start  Search start
     * @param string $end    Search end
     *
     * @return string
     */
    function string_between($string, $start, $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);

        if ($ini == 0) {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }
}

function shortMonthToNumber($shortMonth)
{
    $months = [
        'Jan' => '01',
        'Feb' => '02',
        'Mar' => '03',
        'Apr' => '04',
        'May' => '05',
        'Jun' => '06',
        'Jul' => '07',
        'Aug' => '08',
        'Sep' => '09',
        'Oct' => '10',
        'Nov' => '11',
        'Dec' => '12',
    ];

    if (! array_key_exists($shortMonth, $months)) {
        return false;
    }

    return $months[$shortMonth];
}

function longMonthToNumber($longMonth): string
{
    $months = [
        'January' => '01',
        'February' => '02',
        'March' => '03',
        'April' => '04',
        'May' => '05',
        'June' => '06',
        'July' => '07',
        'August' => '08',
        'September' => '09',
        'October' => '10',
        'November' => '11',
        'December' => '12',
    ];

    if (! array_key_exists($longMonth, $months)) {
        return false;
    }

    return $months[$longMonth];
}

/**
 * @param string $date
 *
 * @return bool|string
 */
function normalizeDateName(string $date)
{
    $date = str_replace('/', '-', $date);
    $date = trim($date, '-');
    $exp = explode('-', $date);

    if (count($exp) !== 3) {
        return false;
    }

    $year = normalizeYear($exp[0]);
    $month = normalizeMonth($exp[1]);
    $day = normalizeDay($exp[2]);

    if (! $year || ! $month || ! $day) {
        return false;
    }

    $cd = \Illuminate\Support\Carbon::create($year, $month, $day);
    $now = \Illuminate\Support\Carbon::now();

    if ($cd > $now) {
        return false;
    }

    $d = $year.'-'.$month.'-'.$day;

    try {
        \Illuminate\Support\Carbon::parse($d);
    } catch (\Exception $e) {
        return false;
    }

    return $d;
}

/**
 * @param string $day
 *
 * @return mixed|string
 */
function normalizeDay(string $day): string
{
    $dayLen = strlen($day);

    if ($dayLen > 2) {
        $day = str_replace('th', '', $day);
    }

    if ($dayLen === 1) {
        $day = '0' . $day;
    }

    return $day;
}

/**
 * @param string $month
 *
 * @return string
 */
function normalizeMonth(string $month): string
{
    $monthLen = strlen($month);

    if ($monthLen === 1) {
        $month = '0' . $month;
    }

    if ($monthLen > 2) {
        if ($monthLen === 3) {
            $month = shortMonthToNumber($month);
        }

        if ($monthLen > 3) {
            $month = longMonthToNumber($month);
        }
    }

    return $month;
}

/**
 * @param string $year
 *
 * @return string
 */
function normalizeYear(string $year): string
{
    $yearLen = strlen($year);
    $now = \Illuminate\Support\Carbon::now();
    $y2d = (int) substr($now->year, 2, 2);

    if ($yearLen !== 4) {
        if ($yearLen === 3) {
            $year = $year[0] === '0' ? '2' . $year : '1' . $year;
        }

        if ($yearLen === 2) {
            $year = (int) $year <= $y2d ? '20' . $year : '19' . $year;
        }
    }

    return $year;
}
