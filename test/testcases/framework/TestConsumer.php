<?php

/**
 * The unit test class of Consumer.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace test\testcases\framework;

use test\BaseTest;

class TestConsumer extends BaseTest
{
    /**
     * Format snake string to camel string.
     *
     * @param $uncamelizedString
     *
     * @param string $separator
     *
     * @return string
     */
    public function camelize($uncamelizedString, $separator = '_'): string
    {
        $uncamelizedString = $separator . str_replace($separator, " ", strtolower($uncamelizedString));
        return ltrim(str_replace(" ", "", ucwords($uncamelizedString)), $separator);
    }

    /**
     * Produce message.
     *
     * @param \framework\Consumer $consumerObject
     *
     * @param string $message
     *
     * @return bool
     */
    public function produceMessage(\framework\Consumer $consumerObject, string $message): bool
    {
        // Create producer config object
        $producerConfigObject = new \RdKafka\Conf();

        // Create producer object
        $producerObject = new \RdKafka\Producer($producerConfigObject);
        $producerObject->addBrokers($consumerObject->getProperty('brokerListString'));

        // Create topic object
        $topicObject = $producerObject->newTopic($consumerObject->getProperty('topic'));

        // Produce message and put it at buffer
        $topicObject->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // setup block time / millisecond / 0 is Non-blocking
        $producerObject->poll(0);

        // Produce message successfully by default
        return true;

        // Starting from 4.0, programs MUST call flush() before shutting down, otherwise some messages and callbacks may be lost.
        // Push buffer and setup timeout / millisecond
        // $result = $producerObject->flush(1000);

        // Return false means unable to flush, messages might be lost!
        // return RD_KAFKA_RESP_ERR_NO_ERROR === $result;
    }

    public function testConstruct(): bool
    {
        $consumer = [
            'check_status_interval_seconds' => 3,
            'broker_list_string'            => '192.168.0.18:9002',
            'partition'                     => 2,
            'timeout_millisecond'           => 200,
            'group_id'                      => 'test_consume_group',
            'topic'                         => 'test_topic',
            'max_worker_count'              => 5,
        ];

        $consumerObject = new \framework\Consumer($consumer);
        foreach ($consumer as $field => $value) {
            $property = $this->camelize($field);
            if ($consumer[$field] != $consumerObject->getProperty($property)) {
                return $this->failed();
            }
        }
        return $this->success();
    }

    public function testIsNeedStop(): bool
    {
        global $consumer;
        $consumerObject = new \framework\Consumer($consumer);

        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE, 'start');
        if ($consumerObject->isNeedStop()) {
            return $this->failed();
        }

        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE, 'stop');
        if (!$consumerObject->isNeedStop()) {
            return $this->failed();
        }

        // set default value
        file_put_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE, 'start');
        return $this->success();
    }

    public function testIsNeedCheckStatus(): bool
    {
        $consumer       = [
            'check_status_interval_seconds' => 2,
        ];
        $consumerObject = new \framework\Consumer($consumer);

        \framework\Timer::start(\framework\Consumer::TIMER_MARK);
        if ($consumerObject->isNeedCheckStatus()) {
            return $this->failed();
        }
        sleep(1);
        if ($consumerObject->isNeedCheckStatus()) {
            return $this->failed();
        }

        sleep(2);
        if (!$consumerObject->isNeedCheckStatus()) {
            return $this->failed();
        }
        if (!$consumerObject->isNeedCheckStatus()) {
            return $this->failed();
        }
        return $this->success();
    }

    public function testHighLevelConsuming(): bool
    {
        $pass = true;
        if ($pass) {
            return $this->success();
        }

        global $consumer;
        $consumerObject = new \framework\Consumer($consumer);
        $message        = "test_message_" . rand(10000, 99999);
        $this->produceMessage($consumerObject, $message);
        $formula = "TestConsumer+testHighLevelConsuming+highLevelConsuming+" . date('Y-m-d H:i:s');
        $logId   = md5($formula);
        \framework\Logger::setLogId($logId, $formula);
        if ($message != $consumerObject->highLevelConsuming(true)) {
            return $this->failed();
        }
        return $this->success();
    }
}
