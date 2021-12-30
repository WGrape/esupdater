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
    /**
     * Encode the canal data to string.
     *
     * @param string $unEncodedCanalData the json format data in kafka queue (canal put it into kafka)
     *
     * @return string
     */
    public function encode(string $unEncodedCanalData): string
    {
        return urlencode($unEncodedCanalData);
    }

    /**
     * Decode the canal data to string.
     *
     * @param string $encodedCanalData the urlencoded canal data
     *
     * @return string
     */
    public function decode(string $encodedCanalData): string
    {
        return urldecode($encodedCanalData);
    }

    /**
     * Parse the canal data to array.
     *
     * @param string $encodedCanalData the urlencoded canal data
     *
     * @return array
     */
    public function parse(string $encodedCanalData): array
    {
        return json_decode($this->decode($encodedCanalData), true);
    }

    /**
     * Check the canal data format.
     *
     * @param array $parsedCanalData
     *
     * @return bool
     */
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
