<?php
/**
 * The bootloader file of esupdater, test and so on.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

// PHP Configuration.
date_default_timezone_set('Asia/Shanghai');

// Define path constants.
const ROOT_PATH      = __DIR__ . '/';
const APP_PATH       = ROOT_PATH . 'app/';
const CONFIG_PATH    = ROOT_PATH . 'config/';
const FRAMEWORK_PATH = ROOT_PATH . 'framework/';
const RUNTIME_PATH   = ROOT_PATH . 'runtime/';
const VENDOR_PATH    = ROOT_PATH . 'vendor/';

// Define file constants.
const RUNTIME_ESUPDATER_CONSUMER_PID_FILE      = RUNTIME_PATH . 'esupdater-consumer.pid';
const RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE   = RUNTIME_PATH . 'esupdater-consumer.status';
const RUNTIME_IGNORE_ERROR_TEMP_FILE           = RUNTIME_PATH . 'ignore-error.temp';
const CREATE_WORKER_LOG_FILE                   = RUNTIME_PATH . 'create-worker.log';
const RUNTIME_ESUPDATER_WORKER_PID_FILE_PREFIX = 'esupdater-worker-';
const COMPOSER_AUTOLOAD_FILE                   = VENDOR_PATH . 'autoload.php';
const ENVIRONMENT_FILE                         = ROOT_PATH . '.env';

// Define data constants.
const DEFAULT_PID = 0;

// Load config files.
include_once CONFIG_PATH . 'consumer.php';
include_once CONFIG_PATH . 'db.php';
include_once CONFIG_PATH . 'es.php';
include_once CONFIG_PATH . 'log.php';
include_once CONFIG_PATH . 'event.php';

/**
 * Include composer autoload file or register autoload.
 */
if (!file_exists(COMPOSER_AUTOLOAD_FILE)) {
    function autoloadCallback(string $classname)
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
} else {
    include_once COMPOSER_AUTOLOAD_FILE;
}

/**
 * Register shutdown callback.
 */
function shutdownCallback()
{
    $manager = new \framework\Manager();

    // Delete the files of consumer process if it exited without delete files.
    if ($manager->isConsumerProcess()) {
        if (file_exists(RUNTIME_ESUPDATER_CONSUMER_PID_FILE)) {
            unlink(RUNTIME_ESUPDATER_CONSUMER_PID_FILE);
        }
        if (file_exists(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE)) {
            unlink(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE);
        }
    }

    // Delete the pid file of worker process if it exited without delete files.
    $workerPIDFile = $manager->isWorkerProcess();
    if ($workerPIDFile !== false && file_exists($workerPIDFile)) {
        unlink($workerPIDFile);
    }
}

register_shutdown_function('shutdownCallback');

/**
 * Register exception callback.
 *
 * @param Throwable $exception
 */
function exception_handler(Throwable $exception = null)
{
    // do something.
}

set_exception_handler('exception_handler');

/**
 * Register error callback.
 *
 * @param int $errNo
 *
 * @param string $errMessage
 *
 * @param string $errFile
 *
 * @param int $errLine
 */
function error_handler(int $errNo, string $errMessage, string $errFile, int $errLine)
{
    // do something.
}

set_error_handler('error_handler');
