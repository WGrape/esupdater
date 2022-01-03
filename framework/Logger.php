<?php
/**
 * This is the only one logger when you need to output logs.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace framework;

class Logger
{
    /**
     * Support log level.
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_SLOW = 'slow';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_FATAL = 'fatal';

    /**
     * The unique id of log.
     *
     * @var string
     */
    private static $logId;

    /**
     * The formula of logId.
     *
     * @var string
     */
    private static $formula;

    /**
     * Setup the logId.
     *
     * @param string $logIdParam
     *
     * @param string $formulaParam
     */
    public static function setLogId(string $logIdParam, string $formulaParam)
    {
        self::$logId   = $logIdParam;
        self::$formula = $formulaParam;
    }

    /**
     * Setup the logId according the parsed canal data.
     *
     * @param array $parsedCanalData
     */
    public static function setLogIdByParsedCanalData(array $parsedCanalData)
    {
        $database = isset($parsedCanalData['database']) ? $parsedCanalData['database'] : '';
        $table    = isset($parsedCanalData['table']) ? $parsedCanalData['table'] : '';
        $type     = isset($parsedCanalData['type']) ? $parsedCanalData['type'] : '';
        $id       = isset($parsedCanalData['id']) ? $parsedCanalData['id'] : 0;
        $ts       = isset($parsedCanalData['ts']) ? $parsedCanalData['ts'] : 0;

        $formula     = self::$formula = "{$database}+{$table}+{$type}+{$id}+{$ts}";
        self::$logId = md5($formula);
    }

    /**
     * Return the debug data.
     *
     * @return array[]
     */
    public static function returnDumpData(): array
    {
        $result = [
            'runtime_files' => [],
        ];

        $handle = opendir(RUNTIME_PATH);
        while ($handle && ($file = readdir($handle)) !== false) {
            if (!is_file($file)) {
                continue;
            }
            $result['runtime_files'][] = $file;
        }
        closedir($handle);

        return $result;
    }

    /**
     * Write log in debug mode.
     *
     * @param string $data the message to write
     */
    public static function logDebug(string $data)
    {
        self::write(self::LEVEL_DEBUG, $data);
    }

    /**
     * Write log in info mode.
     *
     * @param string $data the message to write
     */
    public static function logInfo(string $data)
    {
        self::write(self::LEVEL_INFO, $data);
    }

    /**
     * Write log in slow mode.
     *
     * @param string $data the message to write
     */
    public static function logSlow(string $data)
    {
        self::write(self::LEVEL_SLOW, $data);
    }

    /**
     * Write log in warning mode.
     *
     * @param string $data the message to write
     */
    public static function logWarning(string $data)
    {
        self::write(self::LEVEL_WARNING, $data);
    }

    /**
     * Write log in error mode.
     *
     * @param string $data the message to write
     */
    public static function logError(string $data)
    {
        self::write(self::LEVEL_ERROR, $data);
    }

    /**
     * Write log in fatal mode.
     *
     * @param string $data the message to write
     */
    public static function logFatal(string $data)
    {
        self::write(self::LEVEL_FATAL, $data);
    }

    /**
     * Get the path of log file in different level mode.
     *
     * @param $logLevel
     *
     * @return string
     */
    public static function getLogFilePath($logLevel): string
    {
        global $log;
        $date = date('Ymd');
        if ($logLevel === self::LEVEL_SLOW) {
            return "{$log[$logLevel]['path']}.{$date}";
        }
        return "{$log[$logLevel]}.{$date}";
    }

    /**
     * The common method for writing log.
     *
     * @param $level
     *
     * @param $data
     */
    public static function write($level, $data)
    {
        $logId    = self::$logId;
        $formula  = self::$formula;
        $datetime = date('Y-m-d H:i:s');

        $header = "{$datetime} | logid = {$logId} = {$formula}";
        $body   = $data;
        $footer = "";
        if ($level === self::LEVEL_FATAL) {
            $dumpData = self::returnDumpData();
            $footer   .= "----------Dump is data as follows----------\n";
            $footer   .= ("runtime_files: " . implode(', ', $dumpData['runtime_files']));
        }
        $content = "{$header}\n{$body}\n{$footer}\n";

        $file = self::getLogFilePath($level);
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }
}

