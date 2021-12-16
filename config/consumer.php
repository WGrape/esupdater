<?php

$consume = [
    'check_consume_status_interval_seconds' => 2,
    'broker_list'                           => '127.0.0.1:9092,127.0.0.1:9093',
    'partition_list'                        => [0, 1, 2],
    'consume_timeout_milli_second' => 1000,
    'consume_group' => 'test_group_id',
    'topic' => 'test_topic',
];
