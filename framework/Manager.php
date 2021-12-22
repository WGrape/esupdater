<?php

namespace framework;

class Manager
{
    const COMMAND_WORK_SUCCESS = 'success';
    const COMMAND_STOP_SUCCESS = 'success';
    const COMMAND_STOP_FAILED = 'failed';

    /**
     * Get running workers count
     * @param string $pidFile
     * @return false|int
     */
    public function getRunningWorkersCount($pidFile = "runtime/esupdater-worker-*.pid")
    {
        $tempFile = RUNTIME_IGNORE_ERROR_TEMP_FILE;
        exec("ls {$pidFile} > {$tempFile} 2>&1 && wc -l {$tempFile}", $output);
        @unlink($tempFile); // When call this function, temp file may not exist, so ignore error.
        if (!isset($output[0])) {
            return false;
        }
        $pieces = explode(' ', trim($output[0]));
        return intval($pieces[0]);
    }

    /**
     * Start consumer and blocking
     */
    public function startConsumerAndBlocking()
    {
        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE, Consumer::START_FLAG_STRING);
        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_PID_FILE, intval(getmypid()));

        global $consumer;
        (new Consumer($consumer))->highLevelConsuming();
    }

    /**
     * Stop consumer by IPC(InterProcess Communication): Shared File
     */
    public function stopConsumerByIPC()
    {
        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE, Consumer::STOP_FLAG_STRING);
    }

    /**
     * Whether consumer was stopped or not
     * @return bool
     */
    public function isConsumerStopped(): bool
    {
        return !file_exists(RUNTIME_ESUPDATER_CONSUMER_PID_FILE) && !file_exists(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE);
    }

    /**
     * Whether all workers were stopped or not
     * @return bool
     */
    public function isWorkersStopped(): bool
    {
        return $this->getRunningWorkersCount() < 1;
    }

    /**
     * Command: start
     */
    public function commandStart()
    {
        $formula = "start+" . date('Y-m-d H:i:s');
        $logId   = md5($formula);
        Logger::setLogId($logId, $formula);
        Logger::logInfo('Start esupdater');

        // Start consumer and blocking ...
        $this->startConsumerAndBlocking();

        // After blocking, it means consumer is stopped, so remove consumer runtime files now
        unlink(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE);
        unlink(RUNTIME_ESUPDATER_CONSUMER_PID_FILE);
    }

    /**
     * Command: stop
     * @return string
     */
    public function commandStop(): string
    {
        $formula = "stop+" . date('Y-m-d H:i:s');
        $logId   = md5($formula);
        Logger::setLogId($logId, $formula);
        Logger::logInfo('Stop esupdater');

        // Stop consumer by IPC(InterProcess Communication)
        $this->stopConsumerByIPC();

        // Wait consumer and all workers were stopped, the max wait time is 10 seconds
        $maxWaitSecond  = 10;
        $startTimestamp = time();
        while (true) {
            if ($this->isConsumerStopped() && $this->isWorkersStopped()) {
                Logger::logInfo('Stop esupdater successfully');
                return self::COMMAND_STOP_SUCCESS;
            }
            if ((time() - $startTimestamp) > $maxWaitSecond) {
                Logger::logFatal('Failed to stop esupdater');
                return self::COMMAND_STOP_FAILED;
            }
            sleep(1);
        }
    }

    /**
     * Command: work
     * @param $canalData
     * @return string
     */
    public function commandWork($canalData): string
    {
        (new \framework\Router())->nextHop($canalData);
        return self::COMMAND_WORK_SUCCESS;
    }
}