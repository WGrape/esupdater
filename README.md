<div align="center" >
<img width="200" alt="img" src="https://user-images.githubusercontent.com/35942268/147061994-f0d5a3ec-2d5f-4d72-af1c-139289547f25.png">
</div>

<div align="center">    
    <p>一个基于Canal实现ES文档增量更新的轻量级框架</p>
</div>

<p align="center">
    <img src="https://img.shields.io/badge/PHP-7.0+-blue.svg">
    <img src="https://img.shields.io/badge/Release-1.0.0-blue.svg">
    <a href="https://app.travis-ci.com/github/WGrape/esupdater"><img src="https://app.travis-ci.com/WGrape/esupdater.svg?branch=master"><a>
    <a href="https://wgrape.github.io/esupdater/report.html"><img src="https://img.shields.io/badge/unitest-100%25-yellow.svg"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg"></a>
    <a href="HOWTOCODE.md"><img src="https://img.shields.io/badge/doc-中文-red.svg"></a>
</p>

## 目录

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
- &nbsp;&nbsp;&nbsp;&nbsp;[3、创建事件模块](#43)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、部署项目](#44)
- [五、疑问解答](#5)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、项目文档](#51)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、参与项目](#52)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、提问渠道](#53)

## <span id="1">一、介绍</span>
ESUpdater是一个基于Canal实现ES文档增量更新的轻量级框架。基于以下优势，可以方便的完成业务接入与扩展。

<img width="900" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### <span id="11">1、轻量级框架</span>
从消费Kafka消息，到派发至业务层处理，框架设计清晰，源码简单易懂。

### <span id="12">2、全面容器化</span>
为解决各种依赖安装的复杂困难问题，已实现全面容器化，只需一条命令就可以轻松安装、部署、和维护。

### <span id="13">3、事件驱动化</span>
通过数据表变更事件的注册与回调，完成业务逻辑的实时处理，这种事件驱动化设计更符合增量更新的理念。

## <span id="2">二、快速安装</span>
安装过程会依赖Docker，所以请先安装并启动Docker，或者使用<a href="https://labs.play-with-docker.com/">在线Docker网站</a>，按如下步骤安装即可

### <span id="21">1、获取项目</span>

```bash
git clone https://github.com/WGrape/esupdater
cd esupdater
```

### <span id="22">2、安装依赖</span>

```bash
cd image
bash make.sh
```

如果出现下图提示，则表示```phpkafka```镜像生成成功，至此所有的安装步骤就已经完成。

<img src="https://user-images.githubusercontent.com/35942268/147384280-edb54544-9510-40f8-b9d1-06ddaab7c5c6.png" width="650">


如果安装过程出错，请查看[镜像制作帮助](HELP.md#1)文档。

## 三、<span id="3">轻松管理</span>

### <span id="31">1、容器部署</span>

如果部署出错，请参考[容器部署帮助](HELP.md#2)文档

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
容器的运行时配置在```/start.sh```脚本中定义，请根据实际情况进行修改，或使用默认配置。

| Id | 配置名称 | 配置参数 | 参数值 | 默认值 | 释义 |
| --- | :----:  | :----:  | :---: | :---: | :---: |
| 1 | 核心数 | --cpus | \>=0.5 | 1.5 | 设置允许的最大核心数 |
| 2 | CPU核心集 | ---cpuset-cpus | 0,1,2... | 未设置 | 设置允许执行的CPU核心 |
| 3 | 内存核心集 | --cpuset-mems | 0,1,2... | 未设置 | 设置使用哪些核心的内存 |
| 4 | 目录挂载 | -v  | 磁盘目录 | /home/log/esupdater | 设置容器挂载的目录，以便在宿主机查看日志 |

## <span id="4">四、业务接入</span>

### <span id="41">1、修改配置</span>
只需要修改 [consumer.php](./config/consumer.php) 配置文件中的```broker_list_string```、```group_id```、```topic```这三个必须的配置项即可， 否则无法正常消费数据。

其他非必须的配置请参考[应用配置](./HOWTOCODE.md#3)文档

### <span id="42">2、创建应用</span>

在```/app/```目录下，创建一个以业务为命名规范的应用名称，如```/app/alpha/```

### <span id="43">3、创建事件模块</span>
在上一步中创建的应用目录下，再创建一个由```Handler```和```Service```组成的事件模块，如 ：

- ```/app/alpha/user/UserHandler.php``` ，作用类似 ```Controller```
- ```/app/alpha/user/UserService.php```，作用类似 ```Service```

### <span id="44">4、部署项目</span>
至此业务接入部分已经完成，参考 [轻松管理](#3) 部分部署代码即可

## <span id="5">五、疑问解答</span>

### <span id="51">1、项目文档</span>
项目共有如下3个的文档，以便查看了解

- [README](./README.md) ：项目本身的文档，快速了解项目
- [HOWTOCODE](./HOWTOCODE.md) ：更深的了解项目，包括架构设计、执行原理、应用配置、单元测试等
- [HELP](./HELP.md) ：解决安装和部署过程中问题的帮助手册，包括镜像制作帮助、容器部署帮助等

### <span id="52">2、参与项目</span>
项目源码设计简单易懂，如果你有更好的想法，非常欢迎提出宝贵的 [Pull request](https://github.com/WGrape/esupdater/pulls)

### <span id="53">3、提问渠道</span>
如果在了解和使用过程中，有任何疑问，非常欢迎提出宝贵的 [Issue](https://github.com/WGrape/esupdater/issues/new)
