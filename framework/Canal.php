<?php
/**
 * The common usages of canal.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace framework;

class Canal
{
    public function encode(string $string): string
    {
        return urlencode($string);
    }

    public function decode(string $canalData): string
    {
        return urldecode($canalData);
    }

    public function parse(string $canalData): array
    {
        return json_decode($this->decode($canalData), true);
    }

    public function checkParsedCanalData(array $parsedCanalData): bool
    {
        if (empty($parsedCanalData) || empty($parsedCanalData['data'])) {
            return false;
        }
        if (!isset($parsedCanalData['database']) || !isset($parsedCanalData['table']) || !isset($parsedCanalData['type'])) {
            return false;
        }
        if (!isset($parsedCanalData['id']) || !isset($parsedCanalData['ts'])) {
            return false;
        }
        return true;
    }
}
