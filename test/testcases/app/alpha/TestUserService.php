<?php

namespace test\testcases\app\alpha;

use test\TestLibrary;

class TestUserService extends TestLibrary
{
    public function testGetUserId(): bool
    {
        $caseList = [
            [
                'data'   => 9876543,
                'except' => 987654305,
            ],
            [
                'data'   => 6354232,
                'except' => 635423205,
            ],
        ];
        $service  = new \app\alpha\user\UserService();
        foreach ($caseList as $case) {
            $data   = $case['data'];
            $except = $case['except'];
            if ($except != $service->getUserId($data)) {
                return $this->failed();
            }
        }
        return $this->success();
    }
}