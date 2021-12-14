<?php

namespace framework;

class Manager
{
    /**
     * 开始消费
     */
    public function startConsume()
    {
        file_put_contents(RUNTIME_CONSUMER_STATUS_FILE, 'start');
        file_put_contents(RUNTIME_CONSUMER_PID_FILE, intval(getmypid()));
        (new Consumer)->start();
    }

    /**
     * 暂停消费
     */
    public function stopConsume()
    {
        file_put_contents(RUNTIME_CONSUMER_STATUS_FILE, 'stop');
        file_put_contents(RUNTIME_CONSUMER_PID_FILE, DEFAULT_PID);
    }

    /**
     * 拉取最新代码
     */
    public function gitPull()
    {
        exec('git pull');
    }

    /**
     * 命令: 启动
     */
    public function commandStart()
    {
        $this->gitPull();
        $this->startConsume();
    }

    /**
     * 命令: 停止
     */
    public function commandStop()
    {
        $this->stopConsume();
        sleep(5);
    }

    /**
     * 命令: 重启启动
     */
    public function commandRestart()
    {
        $this->commandStop();
        $this->commandStart();
    }
}