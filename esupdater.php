<?php

include_once 'bootstrap.php';

$command = strtolower(isset($argv[1]) ? $argv[1] : '');
if (empty($command)) {
    echo "命令不能为空";
    return;
}

$manager = new \framework\Manager();
switch ($command) {
    case "start":
        $manager->commandStart();
        break;
    case "stop":
        $manager->commandStop();
        break;
    case "restart":
    case "update":
        $manager->commandRestart();
        break;
    case "run":
        $canalData = isset($argv[2]) ? $argv[2] : "";
        if (empty($canalData)) {
            return;
        }
        (new \Framework\Router())->nextHop($canalData);
        break;
    default:
        echo "不支持的命令: {$command}";
        return;
}
