# 业务接入文档

- [一、快速接入](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、修改配置](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、创建应用](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、创建事件回调](#13)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、注册事件回调](#14)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、部署项目](#15)
- [二、应用配置](#2)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、消费配置](#21)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、数据库配置](#22)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、ES配置](#23)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、日志配置](#24)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、事件配置](#25)
- &nbsp;&nbsp;&nbsp;&nbsp;[6、单测配置](#26)
- [三、系统变量](#3)
- [四、部署管理](#4)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、容器化部署](#41)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、运行时配置](#42)
- [五、单元测试](#5)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、手动测试](#51)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、自动测试](#52)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、添加用例](#53)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、测试报告](#54)

## 一、快速接入

### <span id="11">1、修改配置</span>
只需要修改 [consumer.php](./config/consumer.php) 配置文件中的```broker_list_string```、```group_id```、```topic```这三个必须的配置项即可， 否则无法正常消费数据。

其他非必须的配置请参考[应用配置](#2)文档。

### <span id="12">2、创建应用</span>

在```/app/```目录下，创建一个以业务为命名规范的应用名称，如```/app/alpha/```。

### <span id="13">3、创建事件回调</span>
在上一步中创建的应用目录下，创建一个```Handler```事件回调类

- [/app/alpha/user/UserHandler.php](./app/alpha/user/UserHandler.php) ：作用类似 ```Controller```

如果需要在事件回调中做大量复杂的业务操作，可以创建一个对应的```Service```业务处理类 ：

- [/app/alpha/user/UserService.php](./app/alpha/user/UserService.php) ：作用类似 ```Service```

建议无论业务是否复杂，都把业务放在```Service```中操作。

> 1、在业务Service中可以自由的调用```common```应用下的```DBService```、```ESService```等服务
>
> 2、如果业务更复杂，可以考虑在应用目录下设计属于自己的业务分层，如```daos```、```services```等

### <span id="14">4、注册事件回调</span>
在```/config/event.php```配置文件中添加一个新的键值对，表示当```数据库.数据表```出现变更事件时，由对应的```事件Handler```响应处理。

```php
$event = [
    // 当alpha数据库中的user表发生INSERT/UPDATE/DELETE事件时,
    // 系统会自动创建\app\alpha\user\UserHandler事件回调类,
    // 并根据不同的事件类型调用不同的方法, 如INSERT事件则调用回调类的onInsert()方法
    'alpha.user' => '\app\alpha\user\UserHandler',
];
```

除此之外，框架还支持更加强大的事件注册和驱动机制，如果需要请参考[高级事件配置](#251)。

### <span id="15">5、部署项目</span>
至此业务接入部分已经完成，参考 [部署管理](#3) 部分部署代码即可。

## 二、应用配置

### <span id="21">1、消费配置</span>

配置文件 ```/config/consumer.php```，设置Kafka的消费配置

```php
<?php

$consumer = [
    // 检测消费状态的触发数, 单位为秒
    'check_status_interval_seconds' => 2,
    // broker服务器列表,如果多个则以逗号分割，如192.168.0.18:9092,192.168.0.18:9093
    'broker_list_string'            => '192.168.0.18:9092',
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

### <span id="22">2、数据库配置</span>
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

### <span id="23">3、ES配置</span>
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

### <span id="24">4、日志配置</span>

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

### <span id="25">5、事件配置</span>
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

#### <span id="251">(1) 高级事件配置</span>
上面的这种Key所对应的Value为字符串的配置方式，是一种简单的自动回调配置。 如果Value是Map时，就会使用高级事件配置。

这个Map会再次以如```onInsert```、```onUpdate```、```onDelete```不同的事件为key，value则由以下几种回调函数组成，分别为 ：

- ```filter``` 过滤器 \[可选\] ：实现对Canal数据的过滤处理、对事件回调的拦截
- ```callback``` 事件回调 \[可选\] ：实现事件的回调处理
- ```finally``` 末尾执行 \[可选\] ：实现事件的兜底处理，可用于统计数据、记录日志等

关于高级事件配置可以参考 [高级配置示例](../config/event.php) 。

### <span id="26">6、单测配置</span>
配置文件 ```config/test.php```，如下所示

```php
<?php

$test = [
    // 所有单元测试用例所在的统一目录
    'testcases_directory' => 'test/testcases/',
];
```

## <span id="3">三、系统变量</span>
在```/.env```文件中记录了服务所需要的所有系统变量，在执行```install.sh```安装脚本时完成系统变量的设置，并由```/framework/Environment.php```类解析并处理。

## <span id="4">四、部署管理</span>

### <span id="41">1、容器化部署</span>

如果部署过程中出错，请参考[容器部署帮助](HELP.md#3)文档。

#### <span id="411">(1) 启动</span>

```bash
bash ./start.sh
```

#### <span id="412">(2) 停止</span>

```bash
bash ./stop.sh
```

#### <span id="413">(3) 重启</span>

```bash
bash ./restart.sh
```

### <span id="42">2、运行时配置</span>
可以在```/start.sh```脚本中执行```docker run```时设置```核心数```、```目录挂载```等参数，请自定义修改。

如果需要设置更多的容器参数，可以参考[官方文档](https://docs.docker.com/config/containers/resource_constraints/) 。

| Id | 配置名称 | 配置参数 | 参数值 | 默认值 | 释义 |
| --- | :----:  | :----:  | :---: | :---: | :---: |
| 1 | 核心数 | --cpus | float | 1.5 | 设置允许的最大核心数 |
| 2 | CPU核心集 | --cpuset-cpus | int | 未设置 | 设置允许执行的CPU核心 |
| 3 | 内存核心集 | --cpuset-mems | int | 未设置 | 设置使用哪些核心的内存 |
| 4 | 目录挂载 | -v  | string | /home/log/esupdater | 设置容器挂载的目录 |

## <span id="5">五、单元测试</span>
根目录下的```/test```目录是单元测试目录，其中有一个```/test/run.php```入口文件，它会自动执行 [testcases_directory](HOWTOCODE.md#36) 目录下所有的测试用例。

### <span id="51">1、手动测试</span>
```bash
php test/run.php
```

### <span id="52">2、自动测试</span>
```bash
cp test/prepare-commit-msg ./.git/hooks
chmod +x .git/hooks/prepare-commit-msg

# 此后提交代码会自动执行单元测试，只有单测成功才会允许提交代码
git add .
git commit -m "add: xxx"
```

如下图实际使用中，每次Commit代码会自动执行测试。

<img width="500" alt="img" src="https://user-images.githubusercontent.com/35942268/152677495-1aae134b-93b2-443f-b5cf-8daa719f35f6.png">

### <span id="53">3、添加用例</span>
在```test/testcases/app```目录下，先创建应用目录（如```alpha```），然后在此目录下以```Test*```开头创建单测文件即可，具体内容可参考 [TestUserService](../test/testcases/app/alpha/TestUserService.php) 单测文件

### <span id="54">4、测试报告</span>
在测试运行结束后，会自动生成一个测试报告```/test/report/index.html```文件，<a href="https://wgrape.github.io/esupdater/report.html">点击这里</a>查看报告
