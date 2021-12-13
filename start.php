<?php

include_once 'bootstrap.php';

$canalData = isset($argv[1]) ? $argv[1] : "";
if (empty($canalData)) {
    return;
}

(new \Framework\Router())->nextHop($canalData);
