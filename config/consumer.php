<?php

$consumer = [
    'check_status_interval_seconds' => 2,
    'broker_list_string'            => '127.0.0.1:9092,127.0.0.1:9093',
    'partition'                     => 0,
    'timeout_millisecond'           => 2 * 1000,
    'group_id'                      => '',
    'topic'                         => '',
    'max_worker_count'              => 10,
];
