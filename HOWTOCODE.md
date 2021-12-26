## 一、架构设计

### <span id="11">1、基于Canal</span>
Canal提供了数据库增量订阅与消费的功能，不需要业务代码的侵入和依赖，通过读取MQ，即可获取到数据库的增量更新

### <span id="12">2、ES文档更新</span>
对于数据源为数据库（如MySQL）的ES文档更新，主要有全量更新和增量更新两种方案

- 全量更新 ：脚本全量查询数据库，统一写入至ES中

- 增量更新 ：双写或读取```binlog```，实现ES的增量更新

ESUpdater就是读取```binlog```，实现ES文档增量更新的一种解决方案

### <span id="13">3、完整架构</span>
ESUpdater提供了从消费Kafka中的数据库增量数据，到ES文档增量更新的一个完整业务框架，方便业务的扩展。关于设计原理请[参考文档](HOWTOCODE.md)。

- ```Consumer``` 进程 ：订阅Kafka队列，实时获取数据库的增量变更
- ```Worker``` 进程 ：操作业务逻辑，将数据更新至ES文档

<img src="https://user-images.githubusercontent.com/35942268/147027126-1df83ddf-8698-44dd-a988-5499f7eeb063.png" width="625">

## 二、执行原理
ESUpdater的核心由```Consumer```进程和```Worker```进程组成，其中根目录下的```/esupdater.php```为入口文件

### 1、生命周期
```Consumer```进程和```Worker```进程的生命周期都是由命令控制

#### (1) Consumer 
```Consumer```进程由```php esupdater.php start```命令启动，由```php esupdater.php stop```命令停止

#### (2) Worker

> 由于PHP开发不适合使用进程、多线程扩展来破坏程序的稳定性和简单性，所以目前使用```exec```配合```nohup```的方式来实现启动一个新的PHP后台进程执行异步任务。不过不排除以后会使用线程和进程来替代当前的方案的可能。

当```Consumer```进程从Kafka中拿到消息后，会通过```exec```的方式执行```php esupdater work```命令，以启动一个新的PHP进程，即```Worker```进程。

```Worker```进程会分为后台和非后台两种执行方式，使用哪种执行方式取决于当前```Worker```进程的数量，如果少于配置的```max_worker_count```会使用后台执行的方式，否则使用非后台执行的方式。通过这种方式可以在加快消费速度的同时，保证稳定性。

所以Worker进程的启动完全由```Consumer```控制，如果想要停止```Worker```进程，必须先停止```Consumer```进程，然后等待```Worker```进程正常执行结束即可

### 2、命令执行

#### (1) start
当使用```php esupdater.php start```命令时，会启动一个进程，这个进程会以阻塞主进程的方式订阅Kafka消息，所以这个进程叫做```Consumer```进程

```Consumer```进程启动后会先在```/runtime```目录下写```/runtime/esupdater-consumer.pid```文件和```esupdater-consumer.status```文件，分别记录进程它的进程ID和消费状态```start```。

在```Consumer```进程消费kafka消息的同时，会每隔配置的```check_status_interval_seconds```时间检测一次消费状态（```esupdater-consumer.status```文件），当消费状态变为```stop```时，进程会停止消费，此时```Consumer```进程会完全结束。

#### (2) stop
当使用```php esupdater.php stop```命令时，会启动一个进程，这个进程会向```/runtime/esupdater-consumer.status```文件中写入```stop```指定。

然后每隔一秒钟就会检测```Consumer```进程和```Worker```进程是否都已经完全结束，如果已经检测10秒钟还未完全结束就会通知停止失败，否则停止成功。

#### (3) work
当```Consumer```进程使用```php esupdater work```命令启动```Worker```进程时，```Worker```进程会记录下```/runtime/esupdater-worker-{pid}.pid```进程ID文件，只有当结束后才会删除此文件。

## 三、业务开发
ESUpdater的业务开发模式核心是事件驱动。

### <span id="1">1、创建应用目录</span>
> 如果应用目录已存在，跳过此操作即可

在```app/```目录下，创建自己的应用目录，一般以业务名命名，如```app/alpha```

### <span id="2">2、创建事件模块</span>
在```app/alpha/```目录下，再创建事件模块，事件模块由```handler```和```service```等组成

#### <span id="21">(1) 创建Handler</span>

#### <span id="22">(2) 创建Service</span>

## <span id="4">四、参考文档</span>

- 有关```php-rdkafka```的配置可以 [参考文档](https://github.com/arnaud-lb/php-rdkafka)
- 有关```librdkafka```的配置可以 [参考文档](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md)
- 有关```PHP Kafka```类的使用可以 [参考文档](https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-consumertopic.html)
