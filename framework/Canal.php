<?php

namespace framework;

class Canal
{
    public function parse(string $canalData): array
    {
        return json_decode(urldecode($canalData), true);
    }

    public function checkParsedCanalData(array $parsedCanalData): bool
    {
        if (empty($parsedCanalData)) {
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
