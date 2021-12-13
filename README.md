## 一、介绍
ESUpdater是一个基于Canal的ES文档更新组件

<img width="750" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### 1、基于Canal
Canal提供了数据库增量订阅与消费的功能，借此可以通过不依赖业务代码的方式，获取到数据库的所有数据变更

### 2、ES文档更新
一般情况下，ES文档中的数据都会以数据库为数据源。这样当数据库出现变更时，就需要有相应的数据同步策略把变更的部分都同步至ES

### 3、完整架构
ESUpdater提供了从数据库中变更数据的获取，到ES文档更新的一个完整架构，方便业务的扩展

## 二、安装
```bash
git clone https://github.com/WGrape/ESUpdater
cd ESUpdater/deploy
php deploy.php install
```

## 三、使用
ESUpdater的所有使用操作，都需要在```deploy```目录下进行
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

## 四、测试
```bash
cd ESUpdater/test
php test.php
```

## 五、配置

### 1、数据库配置
配置文件 ```/config/db.php```

### 2、ES配置
配置文件 ```/config/es.php```

### 3、日志配置
配置文件 ```/config/log.php```

### 4、路由配置
配置文件 ```/config/router.php```
