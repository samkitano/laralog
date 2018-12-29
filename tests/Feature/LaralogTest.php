<?php

namespace samkitano\Laralog\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use samkitano\Laralog\Facades\Laralog;
use samkitano\Laralog\Exceptions\LaralogException;

class LaralogTest extends TestCase
{
    /** @test */
    public function can_list_existing_logs()
    {
        $list = Laralog::list();

        $this->assertTrue(is_array($list));
        $this->assertEquals(count($this->test_logs), count($list));
        $this->assertEquals($this->test_logs, $list);
    }

    /** @test */
    public function can_list_logs_dates()
    {
        $expected = [
            "2017-06-10",
            "2017-12-02",
            "2018-11-27",
            "2018-12-07",
            "2018-12-17",
            "2018-12-27",
        ];

        $list = Laralog::dates();

        $this->assertTrue(is_array($list));
        $this->assertEquals($expected, $list);
    }

    /** @test */
    public function can_group_logs_by_year_month_day()
    {
        $expected = [
            '2017' => ["Jun" => [0 => "10"], "Dec" => [0 => "02"]],
            '2018' => ["Nov" => [0 => "27"], "Dec" => [0 => "07", 1 => "17", 2 => "27",]],
        ];

        $list = Laralog::group();

        $this->assertTrue(is_array($list));
        $this->assertEquals($expected, $list);
    }

    /** @test */
    public function can_fetch_most_recent_log()
    {
        $expected = 'laravel-2018-12-27.log';
        $result = Laralog::latest();

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function can_processes_latest_log_if_log_name_omitted()
    {
        $result = Laralog::process();
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2018-12-27', $firstKey);
        $this->assertEquals( 'laravel-2018-12-27.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_full_path()
    {
        $log = $this->test_logs[0];
        $result = Laralog::process($log);
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_4digit_year_2digit_month_2digit_day()
    {
        $result = Laralog::process('2017-06-10');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_3digit_year_2digit_month_2digit_day()
    {
        $result = Laralog::process('017-06-10');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_2digit_year_2digit_month_2digit_day()
    {
        $result = Laralog::process('18-11-27');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2018-11-27', $firstKey);
        $this->assertEquals( 'laravel-2018-11-27.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_4digit_year_1digit_month_2digit_day()
    {
        $result = Laralog::process('2017-6-10');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_4digit_year_short_month_2digit_day()
    {
        $result = Laralog::process('2017-Jun-10');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_4digit_year_long_month_2digit_day()
    {
        $result = Laralog::process('2017-June-10');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_4digit_year_short_month_ordinal_day()
    {
        $result = Laralog::process('2017-Jun-10th');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_date_string_4digit_year_short_month_1digit_day()
    {
        $result = Laralog::process('2017-Dec-2');
        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-12-02', $firstKey);
        $this->assertEquals( 'laravel-2017-12-02.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function can_process_a_log_by_carbon_date()
    {
        $date = Carbon::create(2017, 6, 10);

        $result = Laralog::process($date);

        $firstKey = array_keys($result)[0];
        $name = $result[$firstKey]['name'];
        $entries = $result[$firstKey]['entries'];

        $this->assertTrue(is_array($result));
        $this->assertEquals('2017-06-10', $firstKey);
        $this->assertEquals( 'laravel-2017-06-10.log', $name);
        $this->assertTrue(is_array($entries));
    }

    /** @test */
    public function throws_error_on_invalid_name()
    {
        $name = 'bogus';

        $this->expectException(LaralogException::class);
        $this->expectExceptionMessage("Invalid log name!");

        Laralog::process($name);
    }

    /** @test */
    public function throws_an_error_on_invalid_date()
    {
        $name = '2019-10-18';

        $this->expectException(LaralogException::class);
        $this->expectExceptionMessage("Invalid log name!");

        Laralog::process($name);

        $name = '2018-10-32';

        $this->expectException(LaralogException::class);
        $this->expectExceptionMessage("Invalid log name!");

        Laralog::process($name);

        $name = '2018-02-30';

        $this->expectException(LaralogException::class);
        $this->expectExceptionMessage("Invalid log name!");

        Laralog::process($name);

        $name = '20-02-30000';

        $this->expectException(LaralogException::class);
        $this->expectExceptionMessage("Invalid log name!");

        Laralog::process($name);
    }

    /** @test */
    public function throws_an_error_if_log_not_found()
    {
        $path = realpath(Config::get('laralog.path'));
        $this->expectException(\Illuminate\Contracts\Filesystem\FileNotFoundException::class);
        $this->expectExceptionMessage($path.DIRECTORY_SEPARATOR."laravel-2017-06-11.log not found!");

        Laralog::process('2017-06-11');
    }
}
