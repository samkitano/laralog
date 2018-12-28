<?php

namespace samkitano\Laralog;

class Laralog
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    function list()
    {
        return $this->logger->getLogNames();
    }

    function dates()
    {
        return $this->logger->getLogNamesByDate();
    }

    function group()
    {
        return $this->logger->getGroupedLogNames();
    }

    function process($log = null)
    {
        return $this->logger->process($log);
    }

    function latest()
    {
        return $this->logger->getMostRecent();
    }
}
