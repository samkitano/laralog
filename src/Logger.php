<?php

namespace samkitano\Laralog;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use samkitano\Laralog\Exceptions\LaralogException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Logger
{
    /** @var bool|string */
    protected $path;

    /** @var array */
    protected $logNames;

    /** @var array */
    protected $byDate;

    /** @var \Illuminate\Config\Repository|mixed */
    protected $mainArchive;

    /** @var \Illuminate\Config\Repository|mixed */
    protected $mainArchiveExtension;

    /** @var array */
    protected $groupedLogNames;

    /** @var string */
    protected $latestLogName;

    /** @var array */
    protected $log;

    /** @var string */
    protected $rawLog;

    /** @var string */
    protected $currentLog;

    protected $currentLogSize;


    use ProcessLogs;

    /**
     * Logger constructor.
     */
    public function __construct()
    {
        $this->path = $this->path();
        $this->logNames = $this->logNames();
        $this->mainArchive = $this->mainArchive();
        $this->mainArchiveExtension = $this->archiveExtension();
        $this->byDate = $this->byDate();
        $this->groupedLogNames = $this->groupLogs();
        $this->latestLogName = $this->latestLogName();
    }

    /**
     * @return array
     */
    function getGroupedLogNames(): array
    {
        return $this->groupedLogNames;
    }

    /**
     * @return array
     */
    function getLogNames(): array
    {
        return $this->logNames;
    }

    /**
     * @return array
     */
    function getLogNamesByDate(): array
    {
        return $this->sortDates();
    }

    /**
     * @return string
     */
    function getMostRecent(): string
    {
        return $this->latestLogName;
    }

    /**
     * @param $logfile
     *
     * @return array
     */
    function process($logfile): array
    {
        $this->currentLog = $this->getLogFullPath($logfile);
        $this->rawLog = $this->getRawLog();
        $this->currentLogSize = filesize($this->currentLog);

        $this->makeLog();

        return $this->log;
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getRawLog(): string
    {
        if (! File::exists($this->currentLog)){
            throw new FileNotFoundException("{$this->currentLog} not found!");
        }

        return File::get($this->currentLog);
    }

    /**
     * @return array
     */
    protected function sortDates(): array
    {
        $res = $this->byDate;

        natsort($res);

        return $res;
    }

    /**
     * @return array
     */
    protected function logNames(): array
    {
        return glob($this->path.DIRECTORY_SEPARATOR.LL_LOG_SEARCH_PATTERN);
    }

    /**
     * @return bool|string
     */
    protected function path()
    {
        return realpath(config('laralog.path'));
    }

    /**
     * @return array
     */
    protected function byDate(): array
    {
        return $this->extractDates();
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function mainArchive()
    {
        return config('laralog.main_archive', 'laravel.log');
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function archiveExtension()
    {
        $library = config('kompressor.library', 'zip');
        $ext = config('kompressor.'.$library.'.extension', '.zip');

        return $ext;
    }

    /**
     * @return array
     */
    protected function extractDates(): array
    {
        return array_map(function ($file) {
            return preg_replace(
                '/.*('.LL_DATE_PATTERN.').*/',
                '$1',
                basename($file)
            );
        }, $this->logNames);
    }

    /**
     * @return array
     */
    protected function groupLogs(): array
    {
        $res = [];

        foreach ($this->byDate as $date) {
            $d = new Carbon($date);

            $year = $d->format('Y');
            $month = $d->format('M');
            $day = $d->format('d');

            if (empty($res[$year])) {
                $res[$year] = [];
            }

            if (empty($res[$year][$month])) {
                $res[$year][$month] = [];
            }

            $res[$year][$month][] = $day;
        }

        return $res;
    }

    /**
     * @return string
     */
    protected function latestLogName(): string
    {
        return LL_LOG_PREFIX.maxDate($this->byDate).LL_LOG_EXTENSION;
    }

    /**
     * @param $name
     *
     * @return string
     * @throws \samkitano\Laralog\Exceptions\LaralogException
     */
    protected function pathify($name): string
    {
        if ($this->isFullLogPath($name)) {
            return $name;
        }

        if ($this->isCarbonInstance($name)) {
            $name = $name->toDateString();

            return $this->path.DIRECTORY_SEPARATOR.LL_LOG_PREFIX.normalizeDateName($name).LL_LOG_EXTENSION;
        }

        if ($this->isDate($name)) {
            return $this->path.DIRECTORY_SEPARATOR.LL_LOG_PREFIX.normalizeDateName($name).LL_LOG_EXTENSION;
        }

        $validName = $this->isValidName($name);

        if ($validName) {
            return $this->path.DIRECTORY_SEPARATOR.$validName.LL_LOG_EXTENSION;
        }

        throw new LaralogException("Invalid log name!");
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function isFullLogPath(string $name): bool
    {
        return File::exists($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isDate($name): bool
    {
        return $this->validateDate($name);
    }

    /**
     * @return void
     */
    protected function makeLog(): void
    {
        $rawEntries = $this->getLogEntries($this->rawLog);
        $entries = $this->processEntries($rawEntries);
        $date = $this->getLogDate();

        $this->log = [
            $date =>
                [
                    'name' => basename($this->currentLog),
                    'path' => $this->currentLog,
                    'length' => count($entries),
                    'size' => $this->currentLogSize,
                    'entries' => $entries,
                ]
        ];
    }

    /**
     * @param null|string $logfile
     *
     * @return mixed
     */
    protected function getLogFullPath($logfile): string
    {
        $name = $logfile ?? $this->path.DIRECTORY_SEPARATOR.$this->latestLogName;

        return $this->pathify($name);
    }

    /**
     * @return string
     */
    protected function getLogDate(): string
    {
        return $this->extractDate($this->currentLog);
    }

    /**
     * @param string $currentLog
     *
     * @return string
     */
    protected function extractDate(string $currentLog): string
    {
        return substr(basename($currentLog), 8, 10);
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    protected function validateDate(string $date): bool
    {
        $date = normalizeDateName($date);

        if (! $date) {
            return false;
        }

        return $this->validateDateFromFormat($date, 'Y-m-d');
    }

    /**
     * @param string $date
     * @param string $format
     *
     * @return bool
     */
    protected function validateDateFromFormat(string $date, string $format): bool
    {
        $d = Carbon::createFromFormat($format, $date)->toDateString();

        return $d && $d === $date;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isCarbonInstance($name): bool
    {
        return $name instanceof Carbon;
    }

    /**
     * @param $name
     *
     * @return bool|string
     */
    protected function isValidName($name)
    {
        $fixedExt = $this->normalizeLogExtension($name);
        $base = basename($fixedExt);
        $normalized = normalizeDateName($base);

        return $normalized;
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    protected function normalizeLogExtension($fileName): string
    {
        return rtrim($fileName, '.log').LL_LOG_EXTENSION;
    }
}
