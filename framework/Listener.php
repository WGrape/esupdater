<?php
/**
 * This is an event listener for calling(dispatching) handler when insert/update/delete event of database is triggered.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace framework;

class Listener
{
    const TYPE_INSERT = 'INSERT';
    const TYPE_UPDATE = 'UPDATE';
    const TYPE_DELETE = 'DELETE';

    public function dispatch(string $canalData)
    {
        $canalParser     = new Canal();
        $parsedCanalData = $canalParser->parse($canalData);
        Logger::setLogIdByParsedCanalData($parsedCanalData);
        if (!$canalParser->checkParsedCanalData($parsedCanalData)) {
            Logger::logError("Check canal data error");
            return;
        }

        global $event;
        $database = $parsedCanalData['database'];
        $table    = $parsedCanalData['table'];
        $key      = "{$database}.{$table}";
        if (!isset($event[$key]) || !class_exists($event[$key])) {
            Logger::logError("Not found the handler: {$key}");
            return;
        }

        $handler   = new $event[$key];
        $eventType = $parsedCanalData['type'];
        switch ($eventType) {
            case self::TYPE_INSERT:
                if (method_exists($handler, 'onInsert')) {
                    Logger::logInfo("Call onInsert");
                    $handler->onInsert($parsedCanalData);
                }
                break;
            case self::TYPE_UPDATE:
                if (method_exists($handler, 'onUpdate')) {
                    Logger::logInfo("Call onInsert");
                    $handler->onUpdate($parsedCanalData);
                }
                break;
            case self::TYPE_DELETE:
                if (method_exists($handler, 'onDelete')) {
                    Logger::logInfo("Call onInsert");
                    $handler->onDelete($parsedCanalData);
                }
                break;
            default:
                Logger::logError("Not allowed event type : {$eventType}");
                break;
        }
    }
}

