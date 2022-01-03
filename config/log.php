<?php
/**
 * The log outputting configuration.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

$log = [
    'debug'   => '/home/log/esupdater/debug.log',
    'info'    => '/home/log/esupdater/info.log',
    'slow'    => [
        'millisecond' => 500,
        'path'        => '/home/log/esupdater/slow.log',
    ],
    'warning' => '/home/log/esupdater/warning.log',
    'error'   => '/home/log/esupdater/error.log',
    'fatal'   => '/home/log/esupdater/fatal.log',
];
