### 目录
- [一、架构设计](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、基于Canal](#11)  
- &nbsp;&nbsp;&nbsp;&nbsp;[2、ES文档更新](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、完整架构](#13)
- [二、底层原理](#2)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、生命周期](#21)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、命令执行](#22)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、binlog数据处理过程](#23)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、文件目录规范](#24)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、程序设计规范](#25)
- [三、部署过程](#3)
- [四、参考文档](#4)

## <span id="1">一、架构设计</span>

### <span id="11">1、基于Canal</span>
Canal提供了数据库增量订阅与消费的功能，不需要业务代码的侵入和依赖，通过读取MQ，即可获取到数据库的增量更新

### <span id="12">2、ES文档更新</span>
对于数据源为数据库（如MySQL）的ES文档更新，主要有全量更新和增量更新两种方案

- 全量更新 ：脚本全量查询数据库，统一写入至ES中

- 增量更新 ：双写或读取```binlog```，实现ES的增量更新

ESUpdater就是读取```binlog```，实现ES文档增量更新的一种解决方案

### <span id="13">3、完整架构</span>
ESUpdater提供了从消费Kafka中的数据库增量数据，到ES文档增量更新的一个完整业务框架，方便业务的扩展。

- ```Consumer``` 进程 ：订阅Kafka队列，实时获取数据库的增量变更
- ```Worker``` 进程 ：操作业务逻辑，将数据更新至ES文档

<img src="https://user-images.githubusercontent.com/35942268/147027126-1df83ddf-8698-44dd-a988-5499f7eeb063.png" width="625">

## <span id="2">二、底层原理</span>
ESUpdater的核心由```Consumer```进程和```Worker```进程组成，其中根目录下的```/esupdater.php```为入口文件

### <span id="21">1、生命周期</span>
```Consumer```进程和```Worker```进程的生命周期都是由命令控制

#### <span id="211">(1) Consumer</span> 
```Consumer```进程由```php esupdater.php start```命令启动，由```php esupdater.php stop```命令停止

#### <span id="212">(2) Worker</span>
当```Consumer```进程从Kafka中拿到消息后，会通过```exec```的方式执行```php esupdater work```命令，以启动一个新的PHP进程，即```Worker```进程。

```Worker```进程会分为后台和非后台两种执行方式，使用哪种执行方式取决于当前```Worker```进程的数量，如果少于配置的```max_worker_count```会使用后台执行的方式，否则使用非后台执行的方式。通过这种方式可以在加快消费速度的同时，保证稳定性。

所以Worker进程的启动完全由```Consumer```控制，如果想要停止```Worker```进程，必须先停止```Consumer```进程，然后等待```Worker```进程正常执行结束即可

### <span id="22">2、命令执行</span>

#### <span id="221">(1) start</span>
当使用```php esupdater.php start```命令时，会启动一个进程，这个进程会以阻塞主进程的方式订阅Kafka消息，所以这个进程叫做```Consumer```进程

```Consumer```进程启动后会先在```/runtime```目录下写```/runtime/esupdater-consumer.pid```文件和```esupdater-consumer.status```文件，分别记录进程它的进程ID和消费状态```start```。

在```Consumer```进程消费kafka消息的同时，会每隔配置的```check_status_interval_seconds```时间检测一次消费状态（```esupdater-consumer.status```文件），当消费状态变为```stop```时，进程会停止消费，此时```Consumer```进程会完全结束。

#### <span id="222">(2) stop</span>
当使用```php esupdater.php stop```命令时，会启动一个进程，这个进程会向```/runtime/esupdater-consumer.status```文件中写入```stop```指定。

然后每隔一秒钟就会检测```Consumer```进程和```Worker```进程是否都已经完全结束，如果已经检测10秒钟还未完全结束就会通知停止失败，否则停止成功。

#### <span id="223">(3) work</span>
当```Consumer```进程使用```php esupdater work```命令启动```Worker```进程时，```Worker```进程会记录下```/runtime/esupdater-worker-{pid}.pid```进程ID文件，只有当结束后才会删除此文件。

### <span id="23">3、binlog数据处理过程</span>
处理过程为```binlog => canalData => urlencode(canalData)```，可以参考文件 [/framework/Canal.php](../framework/Canal.php)

1. Canal将```binlog```数据解析为```json```格式并投递至kafka
2. Consumer进程消费kafka，使用```urlencode```方式编码获取到的消息数据
3. Consumer进程把编码后的消息数据，传递至Worker进程
4. Worker进程再依次拆解数据即可

### <span id="24">4、文件目录规范</span>

####  <span id="241">(1) 目录结构
- ```app```目录 ：应用目录
- ```config```目录 ：项目的唯一配置入口
- ```doc```目录 ：项目文档目录
- ```framework```目录 ：项目的核心框架目录
- ```install```目录 ：安装目录
- ```runtime```目录 ：服务运行时产生的中间文件目录，如PID文件，但不包括日志文件
- ```test```目录 ：单元测试目录  
- ```/```目录 ：根目录下存放所有上述目录，和必要的一级文件如```.gitignore```文件

####  <span id="242">(2) 文件规范
- ```shell```脚本不能省略```.sh```后缀，且统一以```bash xxx.sh```的方式执行
- 文档统一以大写英文命名，如```README.md``` / ```HELP.md```

### <span id="25">5、程序设计规范</span>
关于设计规范可以参考文章 [漫谈编程之编程规范](https://github.com/WGrape/Blog/issues/25)

- 调用类的时候使用命名空间前缀，不使用在头部声明```use```的方式

## <span id="3">三、部署过程</span>

> 容器化部署方案依赖于```phpkafka```镜像，所以请确保```phpkafka```镜像已经生成。为了避免重复构建耗时，建议把```phpkafka```镜像推到Docker远程仓库中。

容器构建主要通过根目录下的```/Dockerfile```镜像文件，它会基于```phpkafka```镜像构建一个新的镜像，名为```esupdater```。

### <span id="31">1、启动</span>
当执行如下命令时，会使用```/Dockerfile```文件创建```esupdater```镜像，并创建```esupdaterContainer```容器，最后通过在容器中执行```php esupdater.php start```命令实现服务的启动

```bash
bash ./start.sh
```

启动成功后，除命令行输出```Start success```外，在宿主机```/home/log/esupdater/info.log.{date}```日志中会输出启动日志，如下图所示

<img width="700" alt="img" src="https://user-images.githubusercontent.com/35942268/147385923-80cb29e5-225b-4c83-8637-2513d3e17a1d.png">

### <span id="32">2、停止</span>
当执行以下命令时，会先在容器中执行```php esupdater.php stop```命令，等待容器内```Consumer```进程和```Worker```进程全部停止后，删除镜像和容器

```bash
bash ./stop.sh
```

停止成功后，除命令行输出```Stop success```外，同样的在宿主机```/home/log/esupdater/info.log.{date}```日志中会输出停止成功日志，如下图所示

<img width="700" alt="img" src="https://user-images.githubusercontent.com/35942268/147386373-dd4b66ff-60b8-43ab-8c5a-f03148258f27.png">

### <span id="33">3、重启</span>
当执行以下命令时，会先执行```bash stop.sh```命令，再执行```bash start.sh```命令，以防止出现重复启动的问题

```bash
bash ./restart.sh
```

## <span id="4">四、参考文档</span>

- 有关```php-rdkafka```的配置可以 [参考文档](https://github.com/arnaud-lb/php-rdkafka)
- 有关```librdkafka```的配置可以 [参考文档](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md)
- 有关```PHP Kafka```类的使用可以 [参考文档](https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-consumertopic.html)
