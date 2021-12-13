<?php

include_once __DIR__ . '/../bootstrap.php';
include_once 'config.php';

class Deploy
{
    const FILE_EXTENSION_TYPE_PHP = 1;
    const FILE_EXTENSION_TYPE_PYTHON = 2;

    public $consumerStatusFile = DEPLOY_PATH . 'runtime/consumer.status';
    public $consumerExecutionFile;

    public function __construct()
    {
        $consumerExecutionFile = $this->consumerExecutionFile = isset($config['consumer_execution_file']) ? $config['consumer_execution_file'] : '';
        if (empty($consumerExecutionFile) || !file_exists($consumerExecutionFile) || $this->checkFileExtensionType($consumerExecutionFile) === false) {
            echo "消费程序为空或不存在";
            exit();
        }
    }

    /**
     * 检查文件类型
     * @param $filename
     * @return false|int
     */
    public function checkFileExtensionType($filename)
    {
        if (preg_match('/\.php$/', $filename)) {
            return self::FILE_EXTENSION_TYPE_PHP;
        }
        if (preg_match('/\.py$/', $filename)) {
            return self::FILE_EXTENSION_TYPE_PYTHON;
        }
        return false;
    }

    /**
     * 开始消费
     */
    public function startConsume()
    {
        file_put_contents($this->consumerStatusFile, 'start');
        $type = $this->checkFileExtensionType($this->consumerExecutionFile);
        if ($type === self::FILE_EXTENSION_TYPE_PHP) {
            exec("php {$this->consumerExecutionFile}");
        } else if ($type === self::FILE_EXTENSION_TYPE_PYTHON) {
            exec("python {$this->consumerExecutionFile}");
        }
    }

    /**
     * 暂停消费
     */
    public function stopConsume()
    {
        global $consumerStatusFile;
        file_put_contents($consumerStatusFile, 'stop');
        sleep(5);
    }

    /**
     * 拉取最新代码
     */
    public function gitPull()
    {
        exec('git pull');
    }

    /**
     * 命令: 安装
     */
    public function commandInstall()
    {

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


$command = strtolower(isset($argv[1]) ? $argv[1] : '');
if (empty($command)) {
    echo "命令不能为空";
    return;
}

$deploy = new Deploy();
switch ($command) {
    case "install":
        $deploy->commandInstall();
        break;
    case "start":
        $deploy->commandStart();
        break;
    case "stop":
        $deploy->commandStop();
        break;
    case "restart":
        $deploy->commandRestart();
        break;
    default:
        echo "不支持的命令: {$command}";
        return;
}
