<?php

namespace samkitano\Laralog;

class Laralog
{
    /** @var \samkitano\Laralog\Logger */
    protected $logger;

    /**
     * Laralog constructor.
     */
    function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * @return array
     */
    function list(): array
    {
        return $this->logger->getLogNames();
    }

    /**
     * @return array
     */
    function dates(): array
    {
        return $this->logger->getLogNamesByDate();
    }

    /**
     * @return array
     */
    function group(): array
    {
        return $this->logger->getGroupedLogNames();
    }

    /**
     * @param null $log
     *
     * @return array
     */
    function process($log = null): array
    {
        return $this->logger->process($log);
    }

    /**
     * @return string
     */
    function latest(): string
    {
        return $this->logger->getMostRecent();
    }
}
