<div align="center" >
<img width="200" alt="img" src="https://user-images.githubusercontent.com/35942268/147061994-f0d5a3ec-2d5f-4d72-af1c-139289547f25.png">
</div>

<div align="center">    
    <p>基于Canal的ES文档增量更新组件</p>
</div>

<p align="center">
    <img src="https://img.shields.io/badge/PHP-7.0+-blue.svg">
    <a href="https://app.travis-ci.com/github/WGrape/esupdater"><img src="https://app.travis-ci.com/WGrape/esupdater.svg?branch=master"><a>
</p>

## 目录

- [一、介绍](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、基于Canal](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、ES文档更新](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、完整架构](#13)
- [二、快速安装](#2)
- [三、部署项目](#3)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、非容器化方案](#31)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、容器化方案](#32)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、容器运行配置](#33)  
- [四、如何开发](#4)
- [五、单元测试](#5)
- [六、应用配置](#6)

## <span id="1">一、介绍</span>
ESUpdater是一个基于Canal的ES文档增量更新组件

<img width="900" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### <span id="11">1、基于Canal</span>
Canal提供了数据库增量订阅与消费的功能，借此可以通过不依赖业务代码的方式，获取到数据库的所有数据变更

### <span id="12">2、ES文档更新</span>
一般情况下，ES文档中的数据都会以数据库为数据源。这样当数据库出现变更时，就需要有相应的数据同步策略把变更的部分都同步至ES

### <span id="13">3、完整架构</span>
ESUpdater提供了从数据库中变更数据的获取，到ES文档更新的一个完整业务框架，方便业务的扩展。关于设计原理请[参考文档](./HOWTOCODE.md)。

- ```Consumer``` 进程 ：订阅Kafka队列，实时获取数据库的增量变更
- ```Worker``` 进程 ：操作业务逻辑，将数据更新至ES文档

<img src="https://user-images.githubusercontent.com/35942268/147027126-1df83ddf-8698-44dd-a988-5499f7eeb063.png" width="700">

## <span id="2">二、快速安装</span>

### <span id="21">1、获取项目</span>

通过以下命令获取项目即可

```bash
git clone https://github.com/WGrape/esupdater
cd esupdater
```

### <span id="22">2、安装依赖</span>

> 强烈建议使用容器化部署方案（Docker），摆脱繁杂的依赖安装！

ESUpdater有下述依赖项，如果选择非容器化部署方案，需要自行依次安装。

- PHP扩展 ：```rdkafka-3.0.0```
- Kafka库 ：```librdkafka-dev=0.9.3-1```

如果选择容器化部署方案，在```/esupdater/image```目录中已提供了开箱可用的```phpkafka```镜像文件，只需要简单的执行```bash make.sh```命令即可快速生成```phpkafka```镜像。在成功生成```phpkafka```镜像后，至此所有的安装步骤就已经完成。

## 三、<span id="3">部署项目</span>
部署过程主要由```启动```，```停止```，```重启```三个操作组成

- ```启动``` ：创建Consumer进程
- ```停止``` ：先停止Consumer进程，然后等待所有Worker进程结束，Consumer进程和Worker进程都结束后，整个服务才会停止
- ```重启``` ：```停止```命令和```启动```命令的简单组合

### <span id="31">1、非容器化方案</span>
使用非容器化部署方案，需要[安装依赖](#22)。由于依赖安装会遇到很多问题，所以不建议使用非容器化方案。

#### <span id="311">(1) 启动</span>
使用```nohup```命令以进程方式常驻内存

```bash
nohup php esupdater.php start &
```

#### <span id="312">(2) 停止</span>
```bash
php esupdater.php stop
```

#### <span id="312">(3) 重启</span>
考虑到实用性和简洁性，非容器化部署方案的```重启命令```已废弃

### <span id="32">2、容器化方案</span>

> 容器化部署方案依赖于```phpkafka```镜像，所以请确保```phpkafka```镜像已经生成。为了避免重复构建，建议把```phpkafka```镜像推到Docker远程仓库中。

容器化部署方案主要通过根目录下的```/Dockerfile```文件实现，它会基于```phpkafka```镜像构建一个新的镜像，名为```esupdater```

#### <span id="321">(1) 启动</span>

```bash
bash ./start.sh
```

#### <span id="322">(2) 停止</span>

```bash
bash ./stop.sh
```

#### <span id="323">(3) 重启</span>

```bash
bash ./restart.sh
```

### <span id="33">3、容器运行配置</span>
容器的运行时配置在```/start.sh```脚本中定义

| Id | 配置名称 | 配置参数 | 参数值 | 默认值 | 释义 |
| --- | :----:  | :----:  | :---: | :---: | :---: |
| 1 | 核心数 | --cpus=2.5 | 39MB | 2.5 | 设置允许的最大核心数 |
| 2 | CPU核心集 | ---cpuset-cpus="0,1,2,3" | 0,1,2... | 未设置 | 设置允许执行的CPU核心 |
| 3 | 内存核心集 | --cpuset-mems="2,3" | 0,1,2... | 未设置 | 设置使用哪些核心的内存 |
| 4 | 目录挂载 | -v  | {LocalPath:ContainerPath} | /home/log/esupdater | 设置容器挂载的目录 |

## <span id="4">四、业务开发</span>
关于如何开发，请参考[开发文档](./HOWTOCODE.md)

## <span id="5">五、单元测试</span>
根目录下的```/test```目录是单元测试目录，其中有一个```/test/run.php```入口文件，它会自动扫描 [testcases_directory](#66) 目录下所有的测试用例，并依次执行。


### <span id="51">1、运行测试</span>

```bash
php test/run.php
```

### (1) Travis CI
根目录下的```.travis.yml```文件已配置Travis CI，每次代码提交到```testing```和```master```分支，会自动执行单测

### (2) Git Commit Hook

<img width="600" src="https://user-images.githubusercontent.com/35942268/147193803-3d31df4e-8085-429f-8cbb-08a3509f76e3.png">

在本地开发时，为避免每次手动执行单元测试，可以配置在每次提交代码时，自动执行单元测试。

项目自带了```/prepare-commit-msg```文件，在项目根目录下执行以下命令即可实现！

```bash
cp prepare-commit-msg ./.git/hooks
chmod +x .git/hooks/prepare-commit-msg
```

### <span id="52">2、添加用例</span>
在```test/testcases/app```目录下，先创建应用目录（如```alpha```），然后在此目录下以```Test*```开头创建单测文件即可，具体内容可参考 [TestUserService](./test/testcases/app/alpha/TestUserService.php) 单测文件

## <span id="6">六、应用配置</span>

### <span id="61">1、消费配置</span>

配置文件 ```/config/consumer.php```，设置消费Kafka的配置

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

### <span id="62">2、数据库配置</span>
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

### <span id="63">3、ES配置</span>
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

### <span id="64">4、日志配置</span>

> 在```/start.sh```启动脚本中，```docker run -v ...``` 会把容器中配置的日志目录挂载到本机相应目录中

配置文件 ```/config/log.php```，配置了不同日志级别的文件路径，如下所示

```php
<?php

$log = [
    'debug'   => '/home/log/esupdater/debug.log',
    'info'    => '/home/log/esupdater/info.log',
    'warning' => '/home/log/esupdater/warning.log',
    'error'   => '/home/log/esupdater/error.log',
    'fatal'   => '/home/log/esupdater/fatal.log',
];
```

### <span id="65">5、路由配置</span>
配置文件 ```/config/router.php```，如下所示

- Key ：```数据库名.表名```
- Value : 对应的```Controller```

表示当此数据表的数据更新时，由对应的```Controller```处理

```php
<?php

$router = [
    // 'database.table' => 'app\xxx\controllers\xxx\XXXController',
    'alpha.user' => '\app\alpha\controllers\user\UserController',
];
```

### <span id="66">6、单测配置</span>
配置文件 ```config/test.php```，如下所示

```php
<?php

$test = [
    // 所有单元测试用例所在的统一目录
    'testcases_directory' => 'test/testcases/',
];
```