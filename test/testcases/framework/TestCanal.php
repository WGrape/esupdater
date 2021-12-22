<?php

namespace test\testcases\framework;

use test\TestLibrary;

class TestCanal extends TestLibrary
{
    public function testCheckParsedCanalData(): bool
    {
        $caseList = [
            [
                'data'   => [],
                'except' => false,
            ],
            [
                'data' => [
                    'database' => 'test',
                    'table'    => '',
                    'type'     => 'update',
                    'id'       => 1,
                    'ts'       => 1,
                ],
                'except' => true,
            ],
        ];
        $service = new \framework\Canal();
        foreach ($caseList as $case) {
            $data   = $case['data'];
            $except = $case['except'];
            if ($except != $service->checkParsedCanalData($data)) {
                return $this->failed();
            }
        }
        return $this->success();
    }
}