<?php

namespace samkitano\Laralog;

trait ProcessLogs
{
    /**
     * @param string $log
     *
     * @return array
     */
    protected function getLogEntries(string $log): array
    {
        $pattern = '/\[' . LL_DATE_PATTERN . ' ' . LL_TIME_PATTERN . '\].*/';

        preg_match_all($pattern, $log, $headers);

        $data = preg_split($pattern, $log);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        return [$headers, $data];
    }

    /**
     * @param array $entries
     *
     * @return array
     */
    protected function processEntries(array $entries): array
    {
        $res = [];
        $c = 0;

        foreach ($entries[0][0] as $header) {
            $res[] = $this->processEntry($header, $entries[1][$c]);
            $c++;
        }

        return $res;
    }

    /**
     * @param string $header
     * @param string $stack
     *
     * @return array
     */
    protected function processEntry(string $header, string $stack): array
    {
        $dt = substr($header, 1, 19);
        $envType = string_between($header, '] ', ':');
        $env = strtok($envType, '.');
        $level = strtolower(string_between($header, '.', ':'));

        $st = strpos($header, '"exception":"');
        $error = substr($header, $st + 13);
        $error = trim(str_replace('[object]', '', $error));
        $error = ltrim($error, '(');
        $error = rtrim($error, ')');

        return [
            'raw_header' => $header,
            'datetime' => $dt,
            'env' => $env,
            'level' => $level,
            'error' => $error,
            'stack' => $stack
        ];
    }
}
