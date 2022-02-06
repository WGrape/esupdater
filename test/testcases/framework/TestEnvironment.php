<?php

/**
 * The unit test class of Environment.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test\testcases\framework;

use test\BaseTest;

class TestEnvironment extends BaseTest
{
    public function testParseEnvFile(): bool
    {
        \framework\Environment::$variableContainer = [];
        if (count(\framework\Environment::$variableContainer) != 0) {
            return $this->failed();
        }
        \framework\Environment::parseEnvFile();
        if (count(\framework\Environment::$variableContainer) != 1) {
            return $this->failed();
        }
        if (\framework\Environment::getSystemVariable('ESUPDATER_LOCAL_IP') != '192.168.12.22') {
            return $this->failed();
        }
        return $this->success();
    }
}
