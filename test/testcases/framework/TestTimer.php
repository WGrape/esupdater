<?php

namespace test\testcases\framework;

use test\TestLibrary;

class TestTimer extends TestLibrary
{
    public function testElapsed(): bool
    {
        \framework\Timer::start('test1');
        \framework\Timer::start('test2');

        sleep(1);
        $elapsedTime1 = \framework\Timer::elapsed('test1');
        $elapsedTime2 = \framework\Timer::elapsed('test2');
        if (intval($elapsedTime1 / 1000) !== 1) {
            return $this->failed();
        }
        if (intval($elapsedTime2 / 1000) !== 1) {
            return $this->failed();
        }

        sleep(1);
        $elapsedTime1 = \framework\Timer::elapsed('test1');
        $elapsedTime2 = \framework\Timer::elapsed('test2');
        if (intval($elapsedTime1 / 1000) !== 2) {
            return $this->failed();
        }
        if (intval($elapsedTime2 / 1000) !== 2) {
            return $this->failed();
        }

        return $this->success();
    }
}