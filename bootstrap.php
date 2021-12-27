<?php

// PHP Configuration
date_default_timezone_set('Asia/Shanghai');

// Define path constants
const ROOT_PATH      = __DIR__ . '/';
const APP_PATH       = ROOT_PATH . 'app/';
const CONFIG_PATH    = ROOT_PATH . 'config/';
const FRAMEWORK_PATH = ROOT_PATH . 'framework/';
const RUNTIME_PATH   = ROOT_PATH . 'runtime/';

// Define file constants
const RUNTIME_ESUPDATER_CONSUMER_PID_FILE      = RUNTIME_PATH . 'esupdater-consumer.pid';
const RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE   = RUNTIME_PATH . 'esupdater-consumer.status';
const RUNTIME_IGNORE_ERROR_TEMP_FILE           = RUNTIME_PATH . 'ignore-error.temp';
const CREATE_WORKER_LOG_FILE                   = RUNTIME_PATH . 'create-worker.log';
const RUNTIME_ESUPDATER_WORKER_PID_FILE_PREFIX = 'esupdater-worker-';

// Define data constants
const DEFAULT_PID = 0;

// Load config files
include_once CONFIG_PATH . 'consumer.php';
include_once CONFIG_PATH . 'db.php';
include_once CONFIG_PATH . 'es.php';
include_once CONFIG_PATH . 'log.php';
include_once CONFIG_PATH . 'event.php';

// register autoload callback
function autoloadCallback($classname)
{
    $classname = str_replace('\\', '/', $classname);

    $file = ROOT_PATH . "{$classname}.php";
    if (file_exists($file)) {
        include_once $file;
    } else {
        echo 'class file' . $classname . 'not found!';
    }
}

spl_autoload_register("autoloadCallback", true, true);

// register shutdown callback
function shutdownCallback()
{
    // do something...
}

register_shutdown_function('shutdownCallback');
