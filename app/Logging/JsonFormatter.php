<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class JsonFormatter
{
    /**
     * Customize the log format for JSON output.
     *
     * @param mixed $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new BaseJsonFormatter());
        }
    }
}
