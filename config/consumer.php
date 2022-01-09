<?php
/**
 * The kafka consuming configuration.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

$consumer = [
    'check_status_interval_seconds' => 2,
    'broker_list_string'            => '',
    'partition'                     => 0,
    'timeout_millisecond'           => 2 * 1000,
    'group_id'                      => '',
    'topic'                         => '',
    'max_worker_count'              => 10,
];
