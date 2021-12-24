<?php

namespace test\testcases\app\core;

use test\TestLibrary;

class TestESService extends TestLibrary
{
    public function testIsSuccess(): bool
    {
        $caseList = [
            [
                'data'   => 200,
                'except' => true,
            ],
            [
                'data'   => 300,
                'except' => false,
            ],
        ];
        $service  = new \app\core\services\ESService('test');
        foreach ($caseList as $case) {
            $data   = $case['data'];
            $except = $case['except'];
            if ($except != $service->isSuccess($data)) {
                return $this->failed();
            }
        }
        return $this->success();
    }

    public function testIsNeedToUpdate(): bool
    {
        $caseList = [
            [
                'data'   => [
                    ['name', 'age'],
                    ['id', 'name', 'age'],
                ],
                'except' => true,
            ],
            [
                'data'   => [
                    ['school'],
                    ['id', 'name', 'age'],
                ],
                'except' => false,
            ],
        ];
        $service  = new \app\core\services\ESService('test');
        foreach ($caseList as $case) {
            $data   = $case['data'];
            $except = $case['except'];
            if ($except != $service->isNeedToUpdate($data[0], $data[1])) {
                return $this->failed();
            }
        }
        return $this->success();
    }
}