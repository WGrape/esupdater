<div align="center" >
<img width="200" alt="img" src="https://user-images.githubusercontent.com/35942268/147061994-f0d5a3ec-2d5f-4d72-af1c-139289547f25.png">
</div>

<div align="center">
    <p>一个基于Canal实现ES文档增量更新的轻量级框架</p>
</div>

<p align="center">
    <img src="https://img.shields.io/badge/php-7.0+-blue.svg">
    <img src="https://img.shields.io/badge/release-v1.1.0-blue.svg">
    <img src="https://img.shields.io/badge/version-v1.x-blue.svg">
    <a href="https://app.travis-ci.com/github/WGrape/esupdater"><img src="https://app.travis-ci.com/WGrape/esupdater.svg?branch=master"><a>
    <a href="https://wgrape.github.io/esupdater/report.html"><img src="https://img.shields.io/badge/unitest-100%25-yellow.svg"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg"></a>
    <a href="HOWTOCODE.md"><img src="https://img.shields.io/badge/doc-中文-red.svg"></a>
</p>

<details>
  <summary>目录</summary>

- [一、介绍](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、轻量级框架](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、全面容器化](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、事件驱动化](#13)
- [二、快速安装](#2)
- [三、轻松管理](#3)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、容器部署](#31)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、容器运行配置](#32)
- [四、业务接入](#4)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、修改配置](#41)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、创建应用](#42)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、创建事件回调](#43)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、注册事件回调](#44)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、部署项目](#45)
- [五、关于项目](#5)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、项目文档](#51)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、参与项目](#52)

</details>

## <span id="1">一、介绍</span>
ESUpdater是一个基于Canal实现ES文档增量更新的轻量级框架。基于以下优势，可以方便的完成业务接入与扩展。

<img width="900" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### <span id="11">1、轻量级框架</span>
无论安装使用，还是代码设计的从消费Kafka消息，到派发至业务层处理，整个框架都非常轻量，源码简单易懂。

### <span id="12">2、全面容器化</span>
为解决各种依赖安装的复杂困难问题，已实现全面容器化，只需一条命令就可以轻松安装、部署、和维护。

### <span id="13">3、事件驱动化</span>
基于框架内部的事件驱动设计，可以轻松地注册数据表变更事件和回调，优雅地实现增量更新。

## <span id="2">二、快速安装</span>
安装过程会依赖Docker，所以请先安装并启动它，或者使用<a href="https://labs.play-with-docker.com/">在线Docker网站</a>，按如下步骤安装即可。如果安装过程中出错，请查看[安装过程帮助](HELP.md#1)文档。

### <span id="21">1、获取项目</span>

```bash
git clone https://github.com/WGrape/esupdater
cd esupdater
```

### <span id="22">2、开始安装</span>

```bash
bash install.sh
```

## 三、<span id="3">轻松管理</span>

### <span id="31">1、容器部署</span>

如果部署过程中出错，请参考[容器部署帮助](HELP.md#2)文档。

#### <span id="311">(1) 启动</span>

```bash
bash ./start.sh
```

#### <span id="312">(2) 停止</span>

```bash
bash ./stop.sh
```

#### <span id="313">(3) 重启</span>

```bash
bash ./restart.sh
```

### <span id="32">2、容器运行配置</span>
可以在```/start.sh```脚本中执行```docker run```时设置如下参数配置容器运行状态，或使用默认配置。

| Id | 配置名称 | 配置参数 | 参数值 | 默认值 | 释义 |
| --- | :----:  | :----:  | :---: | :---: | :---: |
| 1 | 核心数 | --cpus | float | 1.5 | 设置允许的最大核心数 |
| 2 | CPU核心集 | --cpuset-cpus | int | 未设置 | 设置允许执行的CPU核心 |
| 3 | 内存核心集 | --cpuset-mems | int | 未设置 | 设置使用哪些核心的内存 |
| 4 | 目录挂载 | -v  | string | /home/log/esupdater | 设置容器挂载的目录 |

如果需要设置更多的容器参数，可以参考[官方文档](https://docs.docker.com/config/containers/resource_constraints/) 。

## <span id="4">四、业务接入</span>

### <span id="41">1、修改配置</span>
只需要修改 [consumer.php](./config/consumer.php) 配置文件中的```broker_list_string```、```group_id```、```topic```这三个必须的配置项即可， 否则无法正常消费数据。

其他非必须的配置请参考[应用配置](./HOWTOCODE.md#3)文档。

### <span id="42">2、创建应用</span>

在```/app/```目录下，创建一个以业务为命名规范的应用名称，如```/app/alpha/```。

### <span id="43">3、创建事件回调</span>
在上一步中创建的应用目录下，创建一个```Handler```事件回调类

- [/app/alpha/user/UserHandler.php](./app/alpha/user/UserHandler.php) ：作用类似 ```Controller```

如果需要在事件回调中做大量复杂的业务操作，可以创建一个对应的```Service```业务处理类 ：

- [/app/alpha/user/UserService.php](./app/alpha/user/UserService.php) ：作用类似 ```Service```

建议无论业务是否复杂，都把业务放在```Service```中操作。

> 1、在业务Service中可以自由的调用```common```应用下的```DBService```、```ESService```等服务
> 
> 2、如果业务更复杂，可以考虑在应用目录下设计属于自己的业务分层，如```daos```、```services```等

### <span id="44">4、注册事件回调</span>
在```/config/event.php```配置文件中添加一个新的键值对，表示当```数据库.数据表```出现变更事件时，由对应的```事件Handler```响应处理。

```php
$event = [
    // 当alpha数据库中的user表发生INSERT/UPDATE/DELETE事件时,
    // 系统会自动创建\app\alpha\user\UserHandler事件回调类,
    // 并根据不同的事件类型调用不同的方法, 如INSERT事件则调用回调类的onInsert()方法
    'alpha.user' => '\app\alpha\user\UserHandler',
];
```

除此之外，框架还支持更加强大的事件注册和驱动机制，如果需要请参考[高级事件配置](./HOWTOCODE.md#351)。

### <span id="45">5、部署项目</span>
至此业务接入部分已经完成，参考 [轻松管理](#3) 部分部署代码即可。

## <span id="5">五、关于项目</span>

### <span id="51">1、项目文档</span>
项目共有如下6个文档，以方便对项目的快速了解

- [README](./README.md) ：项目本身的文档，快速了解项目
- [CONTRIBUTING](./CONTRIBUTING.md) ：介绍如何参与此项目并贡献  
- [HELP](./HELP.md) ：解决安装和部署过程中问题的帮助手册，包括镜像制作帮助、容器部署帮助等
- [HOWTOCODE](./HOWTOCODE.md) ：更深的了解项目，包括架构设计、底层原理、应用配置、单元测试等
- [QUESTION](./QUESTION.md) ：一些关于项目的疑问解释，如```这个项目有什么用```或```为什么不使用PHPunit和Composer```等

### <span id="52">2、参与项目</span>
项目源码设计简单易懂，如有更好的想法，可参考[如何贡献](./CONTRIBUTING.md)文档，期待提出宝贵的 [Pull request](https://github.com/WGrape/esupdater/pulls)  。

如果在了解和使用过程中，有任何疑问，也欢迎提出宝贵的 [Issue](https://github.com/WGrape/esupdater/issues/new) 。
