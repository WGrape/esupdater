<?php

/**
 * The unit test class of Manager.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test\testcases\framework;

use test\BaseTest;

class TestManager extends BaseTest
{
    public function testGetRunningWorkersCount(): bool
    {
        $manager = new \framework\Manager();
        file_put_contents('runtime/esupdater-worker-1.pid', 1);
        file_put_contents('runtime/esupdater-worker-2.pid', 2);
        file_put_contents('runtime/esupdater-worker-3.pid', 3);
        file_put_contents('runtime/esupdater-worker-4.pid', 4);
        file_put_contents('runtime/esupdater-worker-5.pid', 5);
        if ($manager->getRunningWorkersCount() !== 5) {
            return $this->failed();
        }
        unlink('runtime/esupdater-worker-1.pid');
        unlink('runtime/esupdater-worker-2.pid');
        unlink('runtime/esupdater-worker-3.pid');
        unlink('runtime/esupdater-worker-4.pid');
        unlink('runtime/esupdater-worker-5.pid');
        return $this->success();
    }

    public function testStopConsumerByIPC(): bool
    {
        $manager = new \framework\Manager();

        $manager->stopConsumerByIPC();
        if (file_exists(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE) && file_get_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE) !== \framework\Consumer::STOP_FLAG_STRING) {
            return $this->failed();
        }

        unlink(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE);
        return $this->success();
    }

    public function testIsConsumerStopped(): bool
    {
        $manager = new \framework\Manager();

        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE, \framework\Consumer::START_FLAG_STRING);
        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_PID_FILE, 123456);
        if ($manager->isConsumerStopped()) {
            return $this->failed();
        }

        unlink(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE);
        unlink(RUNTIME_ESUPDATER_CONSUMER_PID_FILE);
        if (!$manager->isConsumerStopped()) {
            return $this->failed();
        }
        return $this->success();
    }

    public function testIsWorkersStopped(): bool
    {
        $manager = new \framework\Manager();

        if (!$manager->isWorkersStopped()) {
            return $this->failed();
        }

        file_put_contents('runtime/esupdater-worker-1.pid', 1);
        file_put_contents('runtime/esupdater-worker-2.pid', 2);
        if ($manager->isWorkersStopped()) {
            return $this->failed();
        }

        unlink('runtime/esupdater-worker-1.pid');
        unlink('runtime/esupdater-worker-2.pid');
        if (!$manager->isWorkersStopped()) {
            return $this->failed();
        }

        return $this->success();
    }
}