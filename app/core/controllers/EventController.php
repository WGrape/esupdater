<?php

namespace app\core\controllers;

use framework\Logger;

class EventController extends BaseController
{
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';

    public function onReceiveParsedCanalData($parsedCanalData)
    {
        $type = $parsedCanalData['type'];
        switch ($type) {
            case self::TYPE_INSERT:
                if (method_exists($this, 'onInsert')) {
                    Logger::logInfo("Call onInsert");
                    $this->onInsert($parsedCanalData);
                }
                break;
            case self::TYPE_UPDATE:
                if (method_exists($this, 'onUpdate')) {
                    Logger::logInfo("Call onInsert");
                    $this->onUpdate($parsedCanalData);
                }
                break;
            case self::TYPE_DELETE:
                if (method_exists($this, 'onDelete')) {
                    Logger::logInfo("Call onInsert");
                    $this->onDelete($parsedCanalData);
                }
                break;
            default:
                Logger::logError("Not allowed type : {$type}");
                break;
        }
    }
}