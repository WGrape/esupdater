## 目录

- [一、介绍](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、基于Canal](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、ES文档更新](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、完整架构](#13)
- [二、快速安装](#2)
- [三、部署项目](#3)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、启动](#31)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、停止](#32)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、重启](#33)
- [四、业务开发](#4)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、创建应用目录](#41)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、创建应用子目录](#42)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、创建新的Handlers](#43)
- [五、单元测试](#5)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、运行测试](#51)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、添加用例](#52)
- [六、应用配置](#6)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、数据库配置](#61)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、ES配置](#62)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、日志配置](#63)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、路由配置](#64)

## <span id="1">一、介绍</span>
ESUpdater是一个基于Canal的ES文档更新组件

<img width="750" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### <span id="11">1、基于Canal</span>
Canal提供了数据库增量订阅与消费的功能，借此可以通过不依赖业务代码的方式，获取到数据库的所有数据变更

### <span id="12">2、ES文档更新</span>
一般情况下，ES文档中的数据都会以数据库为数据源。这样当数据库出现变更时，就需要有相应的数据同步策略把变更的部分都同步至ES

### <span id="13">3、完整架构</span>
ESUpdater提供了从数据库中变更数据的获取，到ES文档更新的一个完整业务架构，方便业务的扩展

## <span id="2">二、快速安装</span>
获取项目后，请执行```install```命令以安装相应的依赖

```bash
git clone https://github.com/WGrape/ESUpdater
cd ESUpdater/deploy
php deploy.php install
```

## 三、<span id="3">部署项目</span>
ESUpdater的所有部署操作，都需要在```deploy```目录下进行
```bash
cd ESUpdater/deploy
```

### <span id="31">1、启动</span>
```bash
php deploy.php start
```

### <span id="32">2、停止</span>
```bash
php deploy.php stop
```

### <span id="33">3、重启</span>
```bash
php deploy.php restart
```

## <span id="4">四、业务开发</span>
ESUpdater的业务开发模式和```MVC```模式类似

### <span id="41">1、创建应用目录</span>
> 如果应用目录已存在，跳过此操作即可

在```app/```目录下，创建自己的应用目录，一般以业务名命名，如```app/user```

### <span id="42">2、创建应用子目录</span>
> 如果应用子目录已存在，跳过此操作即可

在```app/user/```目录下，分别创建```controllers```、```services```目录

### <span id="43">3、创建新的Handlers</span>
以```MVC```模式，分别创建```XXXController```、```XXXService```即可

## <span id="5">五、单元测试</span>

### <span id="51">1、运行测试</span>
```bash
cd ESUpdater/test
php test.php
```

### <span id="52">2、添加用例</span>
在```test/testcases/```目录下，创建自己的应用目录，并以```Test*```开头创建一个测试文件即可，参考如下内容

```php
namespace test\testcases\myapp;

use test\TestLibrary;

class TestApp extends TestLibrary
{
    public function testGetLocation(): bool
    {
        $caseList = [
            [
                'data'   => 100,
                'except' => 'Beijing',
            ],
            [
                'data'   => 200,
                'except' => 'Shanghai',
            ],
        ];
        $service  = new \app\myapp\services\AppService();
        foreach ($caseList as $case) {
            $data   = $case['data'];
            $except = $case['except'];
            if ($except != $service->getLocation($data)) {
                return $this->failed();
            }
        }
        return $this->success();
    }
}
```

## <span id="6">六、应用配置</span>

### <span id="61">1、数据库配置</span>
配置文件 ```/config/db.php```，配置了需要访问数据库的配置

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

### <span id="62">2、ES配置</span>
配置文件 ```/config/es.php```，配置了需要访问ES的配置

```php
<?php

$es = [
    'host'          => 'ES服务host',
    'port'          => 'ES服务端口',
    'user_password' => 'ES服务凭证',
    'doc_type'      => '_doc'
];

```

### <span id="63">3、日志配置</span>
配置文件 ```/config/log.php```，配置了不同日志级别的文件路径，如下所示

```php
<?php

$log = [
    'debug'   => '/home/log/ESUpdater/debug.log',
    'info'    => '/home/log/ESUpdater/info.log',
    'warning' => '/home/log/ESUpdater/warning.log',
    'error'   => '/home/log/ESUpdater/error.log',
    'fatal'   => '/home/log/ESUpdater/fatal.log',
];

```

### <span id="64">4、路由配置</span>
配置文件 ```/config/router.php```，如下所示

- Key ：```数据库名.表名```
- Value : 对应的```Controller```

表示当此数据表的数据更新时，由对应的```Controller```处理

```php
<?php

$router = [
    'database.table' => 'app\xxx\controllers\xxx\XXXController',
];

```

