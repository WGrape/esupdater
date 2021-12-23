<?php

namespace framework;

class Manager
{
    const COMMAND_WORK_SUCCESS = 'success';
    const COMMAND_STOP_SUCCESS = 'success';
    const COMMAND_STOP_FAILED = 'failed';

    /**
     * Get running workers count
     * @notice You must do something when this function return false
     * @return false|int
     */
    public function getRunningWorkersCount()
    {
        $handle = opendir(RUNTIME_PATH);
        if (!$handle) {
            return false;
        }

        $count = 0;
        while (($file = readdir($handle)) !== false) {
            if (strpos($file, RUNTIME_ESUPDATER_WORKER_PID_FILE_PREFIX) === 0) {
                ++$count;
            }
        }
        closedir($handle);
        return $count;
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
        $count = $this->getRunningWorkersCount();
        if ($count === false || $count > 0) {
            return false;
        }
        return true;
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
        $pid           = getmypid();
        $workerPIDFile = "runtime/" . RUNTIME_ESUPDATER_WORKER_PID_FILE_PREFIX . $pid . ".pid";
        file_put_contents($workerPIDFile, intval($pid));

        (new \framework\Router())->nextHop($canalData);

        unlink($workerPIDFile);
        return self::COMMAND_WORK_SUCCESS;
    }
}