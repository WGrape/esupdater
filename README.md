## 一、介绍
ESUpdater是一个基于Canal的ES文档更新组件

<img width="750" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### 1、基于Canal
Canal提供了数据库增量订阅与消费的功能，借此可以通过不依赖业务代码的方式，获取到数据库的所有数据变更

### 2、ES文档更新
一般情况下，ES文档中的数据都会以数据库为数据源。这样当数据库出现变更时，就需要有相应的数据同步策略把变更的部分都同步至ES

### 3、完整架构
ESUpdater提供了从数据库中变更数据的获取，到ES文档更新的一个完整架构，方便业务的扩展

## 二、快速安装
获取项目后，请执行```install```命令以安装相应的依赖

```bash
git clone https://github.com/WGrape/ESUpdater
cd ESUpdater/deploy
php deploy.php install
```

## 三、部署项目
ESUpdater的所有部署操作，都需要在```deploy```目录下进行
```bash
cd ESUpdater/deploy
```

### 1、启动
```bash
php deploy.php start
```

### 2、停止
```bash
php deploy.php stop
```

### 3、重启
```bash
php deploy.php restart
```

## 四、业务开发
ESUpdater的业务开发模式和```MVC```模式类似

### 1、创建应用目录
> 如果应用目录已存在，跳过此操作即可

在```app/```目录下，创建自己的应用目录，一般以业务名命名，如```user```

### 2、创建应用子目录
> 如果应用子目录已存在，跳过此操作即可

在```app/user/```目录下，分别创建```controllers```、```services```目录

### 3、创建新的Handlers
以```MVC```模式，分别创建```XXXController```、```XXXService```即可

## 五、单元测试

### 1、运行测试
```bash
cd ESUpdater/test
php test.php
```

### 2、添加用例
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


## 五、应用配置

### 1、数据库配置
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

### 2、ES配置
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

### 3、日志配置
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

### 4、路由配置
配置文件 ```/config/router.php```，如下所示

- Key ：```数据库名.表名```
- Value : 对应的```Controller```

表示当此数据表发生更新时，由对应的```Controller```处理

```php
<?php

$router = [
    'database.table' => 'app\xxx\controllers\xxx\XXXController',
];

```

