<?php

// define constants
const ROOT_PATH      = __DIR__ . '/';
const APP_PATH       = ROOT_PATH . 'app/';
const CONFIG_PATH    = ROOT_PATH . 'config/';
const DEPLOY_PATH    = ROOT_PATH . 'deploy/';
const FRAMEWORK_PATH = ROOT_PATH . 'framework/';

// include config files
include_once CONFIG_PATH . 'db.php';
include_once CONFIG_PATH . 'es.php';
include_once CONFIG_PATH . 'log.php';
include_once CONFIG_PATH . 'router.php';

// include autoload file
include_once './autoload.php';
