<?php
/**
 * The manager of different environment variables.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace framework;

class Environment
{
    /**
     * Variable container.
     * @var array
     */
    public static $variableContainer = [];

    /**
     * Get system variable.
     * @param $variable
     * @return string
     */
    public static function getSystemVariable($variable): string
    {
        $result = '';
        if (empty(self::$variableContainer)) {
            self::parseEnvFile();
        }
        if (isset(self::$variableContainer[$variable])) {
            $result = self::$variableContainer[$variable];
        }
        return $result;
    }

    /**
     * Parse the .env file.
     */
    public static function parseEnvFile()
    {
        $file    = ENVIRONMENT_FILE;
        $handler = fopen($file, 'r+');
        while (!feof($handler)) {
            $content = trim(fgets($handler));
            $slices  = explode('=', $content);
            if (count($slices) === 2) {
                self::$variableContainer[$slices[0]] = $slices[1];
            }
        }
    }
}
