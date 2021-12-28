<?php
/**
 * Do something when you need to timing.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace framework;

class Timer
{
    public static $container = [];

    /**
     * Return now nowSecondTimestampWithMicrosecond
     * @return float
     */
    public static function getNowSecondTimestampWithMicrosecond(): float
    {
        list($microsecond, $secondTimestamp) = explode(' ', microtime());
        return ((float)$microsecond + (float)$secondTimestamp);
    }

    /**
     * Timer start and return nowSecondTimestampWithMicrosecond
     * @param string $mark
     * @return float
     */
    public static function start(string $mark): float
    {
        self::$container[$mark] = $nowSecondTimestampWithMicrosecond = self::getNowSecondTimestampWithMicrosecond();
        return $nowSecondTimestampWithMicrosecond;
    }

    /**
     * Return elapsed milliseconds from start
     * @param string $mark
     * @return int
     */
    public static function elapsed(string $mark): int
    {
        if (!isset(self::$container[$mark])) {
            return 0;
        }

        $startSecondTimestampWithMicrosecond = self::$container[$mark];
        $nowSecondTimestampWithMicrosecond   = self::getNowSecondTimestampWithMicrosecond();
        return (int)(($nowSecondTimestampWithMicrosecond - $startSecondTimestampWithMicrosecond) * 1000);
    }
}