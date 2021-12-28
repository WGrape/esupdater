<?php
/**
 * The unit test class of app\alpha\user\UserService.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test\testcases\app\alpha;

use test\BaseTest;

class TestUserService extends BaseTest
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