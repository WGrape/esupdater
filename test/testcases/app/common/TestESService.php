<?php
/**
 * The unit test class of app\common\ESService.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test\testcases\app\common;

use test\BaseTest;

class TestESService extends BaseTest
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
        $service  = new \app\common\ESService('test');
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
        $service  = new \app\common\ESService('test');
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