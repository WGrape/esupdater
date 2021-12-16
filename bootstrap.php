<?php

// 定义路径常量
const ROOT_PATH      = __DIR__ . '/';
const APP_PATH       = ROOT_PATH . 'app/';
const CONFIG_PATH    = ROOT_PATH . 'config/';
const FRAMEWORK_PATH = ROOT_PATH . 'framework/';
const RUNTIME_PATH   = ROOT_PATH . 'runtime/';

// 定义文件常量
const RUNTIME_CONSUMER_PID_FILE    = RUNTIME_PATH . 'consumer.pid';
const RUNTIME_CONSUMER_STATUS_FILE = RUNTIME_PATH . 'consumer.status';

// 定义数据常量
const DEFAULT_PID                                   = 0;
const DEFAULT_CHECK_CONSUME_STATUS_INTERVAL_SECONDS = 2;
const DEFAULT_CONSUME_BROKER_LIST                           = [];
const DEFAULT_CONSUME_PARTITION_LIST               = [0];
const DEFAULT_CONSUME_TIMEOUT_MILLISECOND  = 1000; // 1000毫秒=1秒
const DEFAULT_CONSUME_GROUP                = 'default_group';
const DEFAULT_CONSUME_TOPIC                = 'default_topic';

// 加载配置文件
include_once CONFIG_PATH . 'consumer.php';
include_once CONFIG_PATH . 'db.php';
include_once CONFIG_PATH . 'es.php';
include_once CONFIG_PATH . 'log.php';
include_once CONFIG_PATH . 'router.php';

// 自动加载
include_once './autoload.php';
