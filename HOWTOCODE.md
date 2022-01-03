### 目录
- [一、架构设计](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、基于Canal](#11)  
- &nbsp;&nbsp;&nbsp;&nbsp;[2、ES文档更新](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、完整架构](#13)
- [二、底层原理](#2)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、生命周期](#21)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、命令执行](#22)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、binlog数据处理过程](#23)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、目录结构规范](#24)
- [三、应用配置](#3)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、消费配置](#31)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、数据库配置](#32)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、ES配置](#33)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、日志配置](#34)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、事件配置](#35)
- &nbsp;&nbsp;&nbsp;&nbsp;[6、单测配置](#36)
- [四、单元测试](#4)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、手动测试](#41)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、自动测试](#42)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、添加用例](#43)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、测试报告](#44)
- [五、部署过程](#5)
- [六、如何支持composer](#6)
- [七、参考文档](#7)

## <span id="1">一、架构设计</span>

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
处理过程为```binlog => canalData => urlencode(canalData)```，可以参考文件 [/framework/Canal.php](./framework/Canal.php)

1. Canal将```binlog```数据解析为```json```格式并投递至kafka
2. Consumer进程消费kafka，使用```urlencode```方式编码获取到的消息数据
3. Consumer进程把编码后的消息数据，传递至Worker进程
4. Worker进程再依次拆解数据即可

### <span id="24">4、目录结构规范</span>

####  <span id="241">(1) 目录结构
- ```app```目录 ：应用目录
- ```config```目录 ：项目的唯一配置入口
- ```framework```目录 ：项目的核心框架目录
- ```images```目录 ：```phpkafka```镜像目录
- ```runtime```目录 ：服务运行时产生的中间文件目录，如PID文件，但不包括日志文件
- ```test```目录 ：单元测试目录  
- ```/```目录 ：根目录下存放所有上述目录，和必要的一级文件如```.gitignore```文件

####  <span id="242">(2) 文件规范
- ```shell```脚本不能省略```.sh```后缀，且统一以```bash xxx.sh```的方式执行
- 文档统一以大写英文命名，如```README.md``` / ```HELP.md```

## <span id="3">三、应用配置</span>

### <span id="31">1、消费配置</span>

配置文件 ```/config/consumer.php```，设置Kafka的消费配置

```php
<?php

$consumer = [
    // 检测消费状态的触发数, 单位为秒
    'check_status_interval_seconds' => 2,
    // broker服务器列表
    'broker_list_string'            => '127.0.0.1:9092,127.0.0.1:9093',
    // 消费分区
    'partition'                     => 0,
    // 消费超时时间, 单位毫秒
    'timeout_millisecond'           => 2 * 1000,
    // 消费组id
    'group_id'                      => '',
    // 消费主题
    'topic'                         => '',
    // worker的最大进程数
    'max_worker_count'              => 10,
];
```

### <span id="32">2、数据库配置</span>
配置文件 ```/config/db.php```，设置访问数据库的配置

```php
<?php

$db = [
    'database' => [
        'host'     => '数据库地址',
        'port'     => 3306,
        'username' => '用户名',
        'password' => '密码',
        'database' => '数据库',
        'charset'  => 'utf8mb4',
    ]
];
```

### <span id="33">3、ES配置</span>
配置文件 ```/config/es.php```，设置访问ES的配置

```php
<?php

$es = [
    'host'          => 'ES服务host',
    'port'          => 'ES服务端口',
    'user_password' => 'ES服务凭证',
    'doc_type'      => '_doc'
];
```

### <span id="34">4、日志配置</span>

> 在```/start.sh```启动脚本中，```docker run -v ...``` 会把容器中配置的日志目录挂载到本机相应目录中

配置文件 ```/config/log.php```，配置了不同日志级别的文件路径，如下所示

```php
<?php

$log = [
    'debug'   => '/home/log/esupdater/debug.log',
    'info'    => '/home/log/esupdater/info.log',
     'slow'    => [
        'millisecond' => 500, // work进程处理耗时超过500ms则记录慢日志
        'path'        => '/home/log/esupdater/slow.log',
    ],
    'warning' => '/home/log/esupdater/warning.log',
    'error'   => '/home/log/esupdater/error.log',
    'fatal'   => '/home/log/esupdater/fatal.log',
];
```

### <span id="35">5、事件配置</span>
配置文件 ```/config/event.php```，如下所示

- Key ：```数据库名.表名```
- Value : ```Handler```

表示当此数据表的数据更新时，由对应的```Handler```处理

```php
<?php

$event = [
    'alpha.user' => '\app\alpha\user\UserHandler',
];
```

#### <span id="351">(1) 高级事件配置</span>
上面的这种Key所对应的Value为字符串的配置方式，是一种简单的自动回调配置。 如果Value是Map时，就会使用高级事件配置。

这个Map会再次以如```onInsert```、```onUpdate```、```onDelete```不同的事件为key，value则由以下几种回调函数组成，分别为 ：

- ```filter``` 过滤器 \[可选\] ：实现对Canal数据的过滤处理、对事件回调的拦截
- ```callback``` 事件回调 \[可选\] ：实现事件的回调处理
- ```finally``` 末尾执行 \[可选\] ：实现事件的兜底处理，可用于统计数据、记录日志等

### <span id="36">6、单测配置</span>
配置文件 ```config/test.php```，如下所示

```php
<?php

$test = [
    // 所有单元测试用例所在的统一目录
    'testcases_directory' => 'test/testcases/',
];
```

## <span id="4">四、单元测试</span>
根目录下的```/test```目录是单元测试目录，其中有一个```/test/run.php```入口文件，它会自动执行 [testcases_directory](./HOWTOCODE.md#36) 目录下所有的测试用例。

### <span id="41">1、手动测试</span>
```bash
php test/run.php
```

### <span id="42">2、自动测试</span>
```bash
cp test/prepare-commit-msg ./.git/hooks
chmod +x .git/hooks/prepare-commit-msg

# 此后提交代码会自动执行单元测试，只有单测成功才会允许提交代码
git add .
git commit -m "add: xxx"
```

### <span id="43">3、添加用例</span>
在```test/testcases/app```目录下，先创建应用目录（如```alpha```），然后在此目录下以```Test*```开头创建单测文件即可，具体内容可参考 [TestUserService](test/testcases/app/alpha/TestUserService.php) 单测文件

### <span id="44">4、测试报告</span>
在测试运行结束后，会自动生成一个测试报告```/test/report/index.html```文件，<a href="https://wgrape.github.io/esupdater/report.html">点击这里</a>查看报告

## <span id="5">五、部署过程</span>

> 容器化部署方案依赖于```phpkafka```镜像，所以请确保```phpkafka```镜像已经生成。为了避免重复构建耗时，建议把```phpkafka```镜像推到Docker远程仓库中。

容器构建主要通过根目录下的```/Dockerfile```镜像文件，它会基于```phpkafka```镜像构建一个新的镜像，名为```esupdater```。

### <span id="51">1、启动</span>
当执行如下命令时，会使用```/Dockerfile```文件创建```esupdater```镜像，并创建```esupdaterContainer```容器，最后通过在容器中执行```php esupdater.php start```命令实现服务的启动

```bash
bash ./start.sh
```

启动成功后，除命令行输出```Start success```外，在宿主机```/home/log/esupdater/info.log.{date}```日志中会输出启动日志，如下图所示

<img width="700" alt="img" src="https://user-images.githubusercontent.com/35942268/147385923-80cb29e5-225b-4c83-8637-2513d3e17a1d.png">

### <span id="52">2、停止</span>
当执行以下命令时，会先在容器中执行```php esupdater.php stop```命令，等待容器内```Consumer```进程和```Worker```进程全部停止后，删除镜像和容器

```bash
bash ./stop.sh
```

停止成功后，除命令行输出```Stop success```外，同样的在宿主机```/home/log/esupdater/info.log.{date}```日志中会输出停止成功日志，如下图所示

<img width="700" alt="img" src="https://user-images.githubusercontent.com/35942268/147386373-dd4b66ff-60b8-43ab-8c5a-f03148258f27.png">

### <span id="53">3、重启</span>
当执行以下命令时，会先执行```bash stop.sh```命令，再执行```bash start.sh```命令，以防止出现重复启动的问题

```bash
bash ./restart.sh
```

## <span id="6">六、如何支持composer</span>
首先在项目根目录下添加```composer.json```文件，内容如下

```json
{
  "require": {},
  "autoload": {
    "psr-4": {
      "app\\": "app/",
      "framework\\": "framework/",
      "test\\": "test/"
    }
  }
}
```

添加完```composer.json```文件后，执行```composer install```等待自动生成```vendor```。最后修改```bootstrap.php```文件，内容如下

```php

// 加载composer的自动加载文件
include_once ROOT_PATH . 'vendor/autoload.php';

// 删除代码中如下的自动加载部分
/**
 * Register autoload callback.
 *
 * @param string $classname
 */
function autoloadCallback(string $classname)
{
    $classname = str_replace('\\', '/', $classname);

    $file = ROOT_PATH . "{$classname}.php";
    if (file_exists($file)) {
        include_once $file;
    } else {
        echo 'class file' . $classname . 'not found!';
    }
}

spl_autoload_register("autoloadCallback", true, true);
```

至此就完成了对Composer的支持，改动非常少，而且不会影响到核心代码

## <span id="7">七、参考文档</span>

- 有关```php-rdkafka```的配置可以 [参考文档](https://github.com/arnaud-lb/php-rdkafka)
- 有关```librdkafka```的配置可以 [参考文档](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md)
- 有关```PHP Kafka```类的使用可以 [参考文档](https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-consumertopic.html)
