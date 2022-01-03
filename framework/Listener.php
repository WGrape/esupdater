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
    /**
     * Database event type: insert.
     */
    const TYPE_INSERT = 'INSERT';

    /**
     * Database event type: update.
     */
    const TYPE_UPDATE = 'UPDATE';

    /**
     * Database event type: delete.
     */
    const TYPE_DELETE = 'DELETE';

    /**
     * Timer mark of work
     */
    const TIMER_MARK = 'work';

    /**
     * Find and
     * @param string $canalData
     */
    public function dispatch(string $canalData)
    {
        // Timer start.
        Timer::start(self::TIMER_MARK);

        // Parse and check canal data.
        $canalParser     = new Canal();
        $parsedCanalData = $canalParser->parse($canalData);
        Logger::setLogIdByParsedCanalData($parsedCanalData);
        if (!$canalParser->checkParsedCanalData($parsedCanalData)) {
            Logger::logError("Check canal data error");
            return;
        }

        // Check the key is valid.
        global $event;
        $database = $parsedCanalData['database'];
        $table    = $parsedCanalData['table'];
        $key      = "{$database}.{$table}";
        if (!isset($event[$key])) {
            Logger::logError("Not found the valid event config for key: key={$key}");
            return;
        }

        $onWhichEvent = false;
        switch ($parsedCanalData['type']) {
            case self::TYPE_INSERT:
                $onWhichEvent = 'onInsert';
                break;
            case self::TYPE_UPDATE:
                $onWhichEvent = 'onUpdate';
                break;
            case self::TYPE_DELETE:
                $onWhichEvent = 'onDelete';
                break;
        }

        $isAutoCallback = is_string($event[$key]);
        if ($isAutoCallback) {
            $this->autoCallback($key, $onWhichEvent, $parsedCanalData);
        } else {
            $this->manualCallback($key, $onWhichEvent, $parsedCanalData);
        }

        global $log;
        $cost = Timer::elapsed(self::TIMER_MARK);
        if (isset($log['slow']['millisecond']) && $cost > $log['slow']['millisecond']) {
            Logger::logSlow("Work slow: key={$key}, onWhichEvent={$onWhichEvent}, cost={$cost}ms");
        }
    }

    /**
     * Auto callback
     * @param string $key
     * @param string $onWhichEvent
     * @param array $parsedCanalData
     */
    public function autoCallback(string $key, string $onWhichEvent, array $parsedCanalData)
    {
        global $event;
        if ($onWhichEvent === false || !method_exists($event[$key], $onWhichEvent)) {
            Logger::logError("Not found the valid auto callback: key={$key}, type={$parsedCanalData['type']}");
            return;
        }

        $handler = new $event[$key];
        $handler->$onWhichEvent($parsedCanalData);
    }

    /**
     * Manual callback
     * @param string $key
     * @param string $onWhichEvent
     * @param array $parsedCanalData
     */
    public function manualCallback(string $key, string $onWhichEvent, array $parsedCanalData)
    {
        global $event;
        if ($onWhichEvent === false || !isset($event[$key][$onWhichEvent]) || !is_array($event[$key][$onWhichEvent])) {
            Logger::logError("Not found the valid manual callback: key={$key}, type={$parsedCanalData['type']}");
            return;
        }

        $filterResult = true;
        if (isset($event[$key][$onWhichEvent]['filter']) && is_callable($event[$key][$onWhichEvent]['filter'])) {
            $filterResult = $event[$key][$onWhichEvent]['filter']($parsedCanalData);
            if ($filterResult) {
                $parsedCanalData = $filterResult;
            }
        }
        $callbackResult = false;
        if ($filterResult && isset($event[$key][$onWhichEvent]['callback']) && is_callable($event[$key][$onWhichEvent]['callback'])) {
            $callbackResult = $event[$key][$onWhichEvent]['callback']($parsedCanalData);
        }
        if (isset($event[$key][$onWhichEvent]['finally']) && is_callable($event[$key][$onWhichEvent]['finally'])) {
            $event[$key][$onWhichEvent]['finally']($filterResult, $callbackResult);
        }
    }
}

