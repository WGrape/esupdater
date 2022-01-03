<?php
/**
 * The event registering configuration, you can choose autoCallback or manualCallback.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

$event = [

    // You can choose the autoCallback, it's very simple.
    // 'alpha.user' => '\app\alpha\user\UserHandler',

    // You can choose the manualCallback, it's a bit complicated but powerful.
    'alpha.user' => [
        'onInsert' => [
            'callback' => function ($parsedCanalData) {
                return (new \app\alpha\user\UserHandler)->onInsert($parsedCanalData);
            },
        ],
        'onUpdate' => [
            'filter'   => function ($parsedCanalData) {
                if (!isset($parsedCanalData['data'][0]) || $parsedCanalData['data'][0]['id'] < 10000000) {
                    return false;
                }
                $parsedCanalData['data'][0]['name'] .= '_filtered';
                return $parsedCanalData;
            },
            'callback' => function (array $parsedCanalData) {
                return (new \app\alpha\user\UserHandler)->onUpdate($parsedCanalData);
            },
            'finally'  => function ($filterResult, $callbackResult) {
                $filterSuccess   = $filterResult ? 'success' : 'failed';
                $callbackSuccess = $callbackResult ? 'success' : 'failed';
                \framework\Logger::logInfo("Work finally: alpha.user.onInsert.filter is {$filterSuccess}, alpha.user.onInsert.callback is {$callbackSuccess}");
            },
        ],
        'onDelete' => [
            'callback' => function (array $parsedCanalData) {
                return (new \app\alpha\user\UserHandler)->onDelete($parsedCanalData);
            },
        ],
    ],
];
