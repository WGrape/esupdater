<?php
/**
 * The kafka consuming configuration.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

$consumer = [
    'check_status_interval_seconds' => 2,
    'broker_list_string'            => '127.0.0.1:9092',
    'partition'                     => 0,
    'timeout_millisecond'           => 2 * 1000,
    'group_id'                      => 'test-group',
    'topic'                         => 'test-topic',
    'max_worker_count'              => 10,
];
