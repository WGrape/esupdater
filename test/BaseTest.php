<?php
/**
 * The base unit test class.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test;

class BaseTest
{
    /**
     * Test success.
     *
     * @return bool
     */
    protected function success(): bool
    {
        $debugTrace    = debug_backtrace();
        $fileShortName = $this->getCallerFileName($debugTrace);
        $functionName  = $this->getCallerFunctionName($debugTrace);
        echo "Test Success: {$fileShortName} -> {$functionName}\n";
        return true;
    }

    /**
     * Test failed.
     * @param string $err
     *
     * @return bool
     */
    protected function failed($err = ""): bool
    {
        $debugTrace    = debug_backtrace();
        $fileShortName = $this->getCallerFileName($debugTrace);
        $functionName  = $this->getCallerFunctionName($debugTrace);
        echo "Test Failed: {$fileShortName} -> {$functionName}\n";
        if (!empty($err)) {
            echo "$err\n";
        }
        return false;
    }

    /**
     * Return text with success color.
     *
     * @param $text
     *
     * @return string
     */
    public function decorateSuccessText($text): string
    {
        return "\033[32m{$text}\033[0m";
    }

    /**
     * Return text with failed color.
     *
     * @param $text
     *
     * @return string
     */
    public function decorateFailedText($text): string
    {
        return "\033[31;4m{$text}\033[0m";
    }

    /**
     * Get file name of caller.
     *
     * @param array $debugTrace the data of debug_backtrace() return
     *
     * @return string
     */
    protected function getCallerFileName(array $debugTrace): string
    {
        if (empty($debugTrace)) {
            return '';
        }
        $fileFullName  = $debugTrace[0]['file'];
        $sliceList     = explode('/test/', $fileFullName);
        $fileShortName = '';
        if (isset($sliceList[1])) {
            $fileShortName = "/test/" . $sliceList[1];
        }
        return $fileShortName;
    }

    /**
     * Get function name of caller.
     *
     * @param array $debugTrace the data of debug_backtrace() return
     *
     * @return string
     */
    protected function getCallerFunctionName(array $debugTrace): string
    {
        if (!isset($debugTrace[1])) {
            return '';
        }
        return $debugTrace[1]['function'];
    }
}