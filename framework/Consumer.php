<?php
/**
 * The consumer process.
 *
 * @author  wgrape <https://github.com/WGrape>
 * @license https://github.com/WGrape/esupdater/blob/master/LICENSE MIT Licence
 */

namespace framework;

class Consumer
{
    const TIMER_MARK = 'consume';
    const CONSUMER_EXIT_WITH_EMPTY_STRING = '';
    const START_FLAG_STRING = 'start';
    const STOP_FLAG_STRING = 'stop';

    private $checkStatusIntervalSeconds;
    private $brokerListString;
    private $partition;
    private $timeoutMillisecond;
    private $groupId;
    private $topic;
    private $maxWorkerCount;

    public function __construct($consumer)
    {
        $this->checkStatusIntervalSeconds = (isset($consumer['check_status_interval_seconds']) && !empty($consumer['check_status_interval_seconds'])) ? $consumer['check_status_interval_seconds'] : 2;
        $this->brokerListString           = (isset($consumer['broker_list_string']) && !empty($consumer['broker_list_string'])) ? $consumer['broker_list_string'] : ($this->getLocalIP() . ':9092');
        $this->partition                  = (isset($consumer['partition']) && !empty($consumer['partition'])) ? $consumer['partition'] : 0;
        $this->timeoutMillisecond         = (isset($consumer['timeout_millisecond']) && !empty($consumer['timeout_millisecond'])) ? $consumer['timeout_millisecond'] : 2 * 1000;
        $this->groupId                    = (isset($consumer['group_id']) && !empty($consumer['group_id'])) ? $consumer['group_id'] : 'default_group';
        $this->topic                      = (isset($consumer['topic']) && !empty($consumer['topic'])) ? $consumer['topic'] : 'default_topic';
        $this->maxWorkerCount             = (isset($consumer['max_worker_count']) && !empty($consumer['max_worker_count'])) ? $consumer['max_worker_count'] : 100;
    }

    /**
     * Get local host IP
     * @return string
     */
    public function getLocalIP(): string
    {
        $command = "ifconfig -a | grep inet | grep -v 127.0.0.1 | grep -v inet6 | grep '192.168' | head -n 1 | awk '{print $2}' | tr -d \"addr:\"";
        exec($command, $localIP);
        if (stripos($localIP, '192.168')) {
            $localIP = '127.0.0.1';
        }
        return $localIP;
    }

    /**
     * Whether need to stop consume or not
     * @return bool
     */
    public function isNeedStop(): bool
    {
        return file_get_contents(RUNTIME_ESUPDATER_CONSUMER_STATUS_FILE) === self::STOP_FLAG_STRING;
    }

    /**
     * Whether need to check status or not
     * @return bool
     */
    public function isNeedCheckStatus(): bool
    {
        $millisecond = \framework\Timer::elapsed(self::TIMER_MARK);
        return ($millisecond / 1000) >= $this->checkStatusIntervalSeconds;
    }

    /**
     * Get property
     * @param string $propertyName
     * @return string | false | array | int | mixed
     */
    public function getProperty(string $propertyName)
    {
        return property_exists($this, $propertyName) ? $this->$propertyName : '';
    }

    /**
     * DEPRECATED: low level consuming, but now it's not available
     * @param false $onlyForTest
     * @return string
     */
    public function lowLevelConsuming($onlyForTest = false): string
    {
        // Create consumer config object
        $consumerConfigObject = new \RdKafka\Conf();
        $consumerConfigObject->set('group.id', $this->groupId);

        // Create topic config object
        $topicConfigObject = new \RdKafka\TopicConf();
        $topicConfigObject->set('auto.offset.reset', 'smallest');

        // Create consumer object
        $consumerObject = new \RdKafka\Consumer($consumerConfigObject);
        $consumerObject->addBrokers($this->brokerListString);

        // Create topic object
        $topicObject = $consumerObject->newTopic($this->topic, $topicConfigObject);

        // Consume start from last offset
        $topicObject->consumeStart($this->partition, RD_KAFKA_OFFSET_STORED);

        while (true) {
            $message = $topicObject->consume($this->partition, $this->timeoutMillisecond);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    if (is_null($message) || empty($message->payload)) {
                        echo "Message is null or payload is empty\n";
                        continue;
                    }
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timeout\n";
                    break;
                default:
                    echo "Unknown message error\n";
                    break;
            }
        }
    }

    /**
     * High level consuming
     * @param false $onlyForTest
     * @return string
     */
    public function highLevelConsuming($onlyForTest = false): string
    {
        $canal   = new Canal();
        $manager = new Manager();

        // Create topic config object
        $topicConfigObject = new \RdKafka\TopicConf();
        $topicConfigObject->set('request.required.acks', true);
        $topicConfigObject->set('auto.commit.interval.ms', 100);
        $topicConfigObject->set('auto.offset.reset', 'smallest');

        // Create consumer config object
        $consumerConfigObject = new \RdKafka\Conf();
        $consumerConfigObject->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    $kafka->assign($partitions);
                    break;
                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $kafka->assign(NULL);
                    break;
                default:
                    Logger::logError("Consumer setRebalanceCb occurs exception: {$err}");
            }
        });
        $consumerConfigObject->set('group.id', $this->groupId);
        $consumerConfigObject->set('metadata.broker.list', $this->brokerListString);
        $consumerConfigObject->setDefaultTopicConf($topicConfigObject);

        // Create consumer object
        try {
            $consumerObject = new \RdKafka\KafkaConsumer($consumerConfigObject);
            $consumerObject->subscribe([$this->topic]);
        } catch (\Throwable $e) {
            Logger::logFatal("Consumer failed to new KafkaConsumer: " . $e->getMessage());
            return self::CONSUMER_EXIT_WITH_EMPTY_STRING;
        }

        // Timer start
        \framework\Timer::start(self::TIMER_MARK);

        // Consuming loop
        while (true) {
            // Check consume status
            if ($this->isNeedCheckStatus() && $this->isNeedStop()) {
                Logger::logInfo('Consumer need to stop');
                return self::CONSUMER_EXIT_WITH_EMPTY_STRING;
            }

            // Fetch message
            $message = $consumerObject->consume($this->timeoutMillisecond);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    if (is_null($message) || empty($message->payload)) {
                        Logger::logDebug('Consumer fetch message is null or payload is empty');
                        continue;
                    }
                    if ($onlyForTest) {
                        return $message->payload;
                    }

                    $logFile   = CREATE_WORKER_LOG_FILE;
                    $canalData = $canal->encode($message->payload);
                    $count     = $manager->getRunningWorkersCount();
                    if ($count !== false && $count < ($this->maxWorkerCount - 1)) {
                        Logger::logDebug("Consumer handle message: create non-block worker process, current count is {$count}");
                        exec("nohup php esupdater.php work {$canalData} >> {$logFile} &");
                    } else {
                        Logger::logDebug("Consumer handle message: create block worker process, current count is {$count}");
                        exec("php esupdater.php work {$canalData} >> {$logFile}");
                    }
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    Logger::logDebug('Consumer highLevelConsuming fetch no more messages');
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    Logger::logDebug('Consumer highLevelConsuming fetch timeout');
                    break;
                default:
                    Logger::logError('Consumer highLevelConsuming catch unknown message error');
                    break;
            }
        }
    }
}
