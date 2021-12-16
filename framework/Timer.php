<?php

namespace framework;

class Timer
{
    public static function elapsed(string $mark): int
    {
        static $container = array();
        if (!isset($container[$mark])) {
            $container[$mark] = 0;
        }
        list($usec, $sec) = explode(' ', microtime());
        $tmp              = $container[$mark];
        $container[$mark] = ((float)$usec + (float)$sec);
        $result           = (int)(($container[$mark] - $tmp) * 1000);
        return $result;
    }
}