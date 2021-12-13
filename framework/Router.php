<?php

namespace framework;

class Router
{
    public function nextHop(string $canalData)
    {
        $canalParser     = new Canal();
        $parsedCanalData = $canalParser->parse($canalData);
        Logger::setLogIdByParsedCanalData($parsedCanalData);
        if (!$canalParser->checkParsedCanalData($parsedCanalData)) {
            Logger::logFatal("Check canal data error");
            exit();
        }

        global $router;
        $database = $parsedCanalData['database'];
        $table    = $parsedCanalData['table'];
        $key      = "{$database}.{$table}";
        if (!isset($router[$key])) {
            Logger::logError("Not found the next hop");
            exit();
        }

        $whichController = $router[$key];
        $controller      = new $whichController;
        if (method_exists($controller, 'onReceiveParsedCanalData')) {
            Logger::logInfo("Call onReceiveParsedCanalData ======>: " . $canalData);
            $controller->onReceiveParsedCanalData($parsedCanalData);
            Logger::logInfo("<====== END");
        }
    }
}

