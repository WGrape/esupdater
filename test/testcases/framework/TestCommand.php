<?php

namespace test\testcases\framework;

use test\TestLibrary;

class TestCommand extends TestLibrary
{
    public function testWork(): bool
    {
        $canalData = (new \framework\Canal())->encode('{"data":[{"userid":"20292","name":"jack"}],"database":"alpha","es":1639020016000,"id":4967056,"isDdl":false,"mysqlType":{"userid":"int(11)","name":"varchar(50)"},"old":null,"pkNames":["workid"],"sql":"","table":"user","ts":1639020017052,"type":"UPDATE"}');
        exec("php esupdater.php work '{$canalData}'", $output);
        if (!isset($output[0]) || $output[0] !== \framework\Manager::COMMAND_WORK_SUCCESS) {
            return $this->failed();
        }
        return $this->success();
    }
}