<?php

namespace framework;

class Consumer
{
    public $timerMark = 'consume';

    public $checkConsumeStatusIntervalSeconds;
    public $brokerList;
    public $partitionList;
    public $consumeTimeoutMilliSecond;
    public $consumeGroup;
    public $topic;

    public function __construct()
    {
        global $consume;
        $this->checkConsumeStatusIntervalSeconds = isset($consume['check_consume_status_interval_seconds']) ?? DEFAULT_CHECK_CONSUME_STATUS_INTERVAL_SECONDS;
        $this->brokerList                        = isset($consume['broker_list']) ?? DEFAULT_CONSUME_BROKER_LIST;
        $this->partitionList                     = isset($consume['partition_list']) ?? DEFAULT_CONSUME_PARTITION_LIST;
        $this->consumeTimeoutMilliSecond         = isset($consume['consume_timeout_milli_second']) ?? DEFAULT_CONSUME_TIMEOUT_MILLISECOND;
        $this->consumeGroup                      = isset($consume['consume_group']) ?? DEFAULT_CONSUME_GROUP;
        $this->topic                             = isset($consume['topic']) ?? DEFAULT_CONSUME_TOPIC;
    }

    public function isStop(): bool
    {
        return file_get_contents(RUNTIME_CONSUMER_STATUS_FILE) === 'stop';
    }

    public function isNeedCheckStatus(): bool
    {
        $millisecond = \framework\Timer::elapsed($this->timerMark);
        return $millisecond >= $this->checkConsumeStatusIntervalSeconds;
    }

    // https://github.com/arnaud-lb/php-rdkafka
    public function start()
    {
        // 创建consumer配置对象
        $consumerConfigObject = new \RdKafka\Conf();
        $consumerConfigObject->set('group.id', $this->consumeGroup);

        // 创建Topic配置对象
        $topicConfigObject = new \RdKafka\TopicConf();
        $topicConfigObject->set('auto.commit.interval.ms', 100);
        $topicConfigObject->set('auto.offset.reset', 'smallest');

        // 创建Consumer对象
        $consumerObject = new \RdKafka\Consumer($consumerConfigObject);
        $consumerObject->addBrokers($this->brokerList);

        // 创建Topic对象
        $topicObject = $consumerObject->newTopic($this->topic, $topicConfigObject);

        // 开始计时
        \framework\Timer::elapsed($this->timerMark);

        while (true) {
            // 检查消费状态
            if ($this->isNeedCheckStatus() && $this->isStop()) {
                return;
            }

            // 随机选择分区并消费
            $partition = array_rand($this->partitionList, 1);
            $topicObject->consumeStart($partition, RD_KAFKA_OFFSET_STORED);
            $message = $topicObject->consume($partition, $this->consumeTimeoutMilliSecond);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    // exec("nohup php esupdater.php run  >> log.txt &");
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
            }
        }
    }
}