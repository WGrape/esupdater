<?php

/**
 * The unit test class of Canal.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test\testcases\framework;

use test\BaseTest;

class TestCanal extends BaseTest
{
    public function testCheckParsedCanalData(): bool
    {
        $caseList = [
            [
                'data'   => [],
                'except' => false,
            ],
            [
                'data'   => [
                    'data'     => [
                        [],
                    ],
                    'database' => 'test',
                    'table'    => '',
                    'type'     => 'update',
                    'id'       => 1,
                    'ts'       => 1,
                ],
                'except' => true,
            ],
        ];
        $service  = new \framework\Canal();
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