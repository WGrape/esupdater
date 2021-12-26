<?php

namespace framework;

class Logger
{
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_FATAL = 'fatal';

    private static $logId;
    private static $formula;

    public static function setLogId(string $logIdParam, string $formulaParam)
    {
        self::$logId   = $logIdParam;
        self::$formula = $formulaParam;
    }

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

    public static function logDebug(string $data)
    {
        self::write(self::LEVEL_DEBUG, $data);
    }

    public static function logInfo(string $data)
    {
        self::write(self::LEVEL_INFO, $data);
    }

    public static function logWarning(string $data)
    {
        self::write(self::LEVEL_WARNING, $data);
    }

    public static function logError(string $data)
    {
        self::write(self::LEVEL_ERROR, $data);
    }

    public static function logFatal(string $data)
    {
        self::write(self::LEVEL_FATAL, $data);
    }

    public static function generateLogFile($logLevel): string
    {
        global $log;
        $date = date('Ymd');
        return "{$log[$logLevel]}.{$date}";
    }

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

        $file = self::generateLogFile($level);
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }
}

