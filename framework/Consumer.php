<?php

namespace framework;

class Consumer
{
    public $maxConsumeIntervalSeconds;

    public function __construct()
    {
        global $consume;
        $this->maxConsumeIntervalSeconds = isset($consume['check_consume_status_interval_seconds']) ?? DEFAULT_MAX_CONSUME_INTERVAL_SECONDS;
    }

    public function isStop(): bool
    {
        return file_get_contents(RUNTIME_CONSUMER_STATUS_FILE) === 'stop';
    }

    public function isOverMaxIntervalSeconds(): bool
    {
        $millisecond = \Timer::elapsed('consume_elapsed');
        return $millisecond >= $this->maxConsumeIntervalSeconds;
    }

    public function start()
    {
        while (true) {
            if ($this->isOverMaxIntervalSeconds() && $this->isStop()) {
                return;
            }
        }
    }
}