<div align="center" >
<img width="200" alt="img" src="https://user-images.githubusercontent.com/35942268/147061994-f0d5a3ec-2d5f-4d72-af1c-139289547f25.png">
</div>

<div align="center">
    <p>一个基于Canal实现ES文档增量更新的高性能轻量框架</p>
</div>

<p align="center">
    <img src="https://img.shields.io/badge/php-7.0+-blue.svg">
    <img src="https://img.shields.io/badge/release-v2.0.0-blue.svg">
    <img src="https://img.shields.io/badge/version-2.x-blue.svg">
    <a href="https://app.travis-ci.com/github/WGrape/esupdater"><img src="https://app.travis-ci.com/WGrape/esupdater.svg?branch=master"><a>
    <a href="https://wgrape.github.io/esupdater/report.html"><img src="https://img.shields.io/badge/unitest-100%25-yellow.svg"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-green.svg"></a>
    <a href="doc/HOWTOCODE.md"><img src="https://img.shields.io/badge/doc-中文-red.svg"></a>
</p>

- [一、介绍](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、轻量级框架](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、全面容器化](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、事件驱动化](#13)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、高性能消费](#14)  
- [二、快速使用](#2)
- [三、轻松管理](#3)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、容器部署](#31)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、容器运行配置](#32)
- [四、业务接入](#4)
- [五、关于项目](#5)


## <span id="1">一、介绍</span>
ESUpdater是一个基于Canal实现ES文档增量更新的高性能轻量框架。基于以下优势，可以让你快速上手和使用。

<img width="900" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### <span id="11">1、轻量级框架</span>
无论安装使用，还是代码设计，整个框架都非常轻量，源码也保持精粹。

### <span id="12">2、全面容器化</span>
为解决各种依赖安装的复杂麻烦问题，已实现全面容器化，只需一条命令就可以轻松安装、部署、和维护。

### <span id="13">3、事件驱动化</span>
基于框架内部的事件驱动设计，可以轻松地注册不同数据表的变更事件和回调，优雅地实现增量更新。

### <span id="14">4、高性能消费</span>
通过一个```Consumer```进程和多个```Worker```进程的一对多通信模型，实现高性能消费。

## <span id="2">二、快速使用</span>
如下过程中会依赖Docker，所以请先安装并启动它。如果只是试用则强烈建议你全程使用<a href="https://labs.play-with-docker.com/">在线Docker网站</a>，按如下步骤安装即可，非常方便。

### <span id="21">1、获取项目</span>
如果出错请参考[获取过程帮助](doc/HELP.md#12)文档。

```bash
git clone https://github.com/WGrape/esupdater
cd esupdater
```

### <span id="22">2、开始安装</span>
如果出错请参考[安装过程帮助](doc/HELP.md#13)文档。

```bash
cd install
bash install.sh
cd ..
```

### <span id="23">3、修改配置</span>
```bash
vi config/consumer.php

# 在上述安装过程中会在本地帮你自动创建并启动一个kafka
# 所以需要把broker_list_string中的IP地址修改为你本机的IP
# 输入ifconfig即可查看, 一般以192.168开头, 而不是127.0.0.1
# broker_list_string => 192.168.x.x:9092
```

### <span id="24">4、运行项目</span>
如果出错请参考[运行过程帮助](doc/HELP.md#3)文档。

```bash
bash start.sh

# 查看日志输出
tail -f /home/log/esupdater/debug.log.20220111
```

### <span id="25">5、测试运行</span>
在另一个窗口进入```kafkaContainer```容器中，按如下操作启动```Kafka生产者```

```bash
docker exec -it kafkaContainer /bin/bash
cd /opt/kafka/
./bin/kafka-console-producer.sh --broker-list localhost:9092 --topic default_topic
```

<img width="843" alt="img1" src="https://user-images.githubusercontent.com/35942268/148804272-b00483a9-3861-4aab-8b2f-aee963784694.png">

启动成功后会进入一个生产消息的命令行，发送任意消息后，查看上一步日志中的输出，如果出现如下类似日志则说明服务已经成功运行 ！

<img width="823" alt="img2" src="https://user-images.githubusercontent.com/35942268/148806227-25af15b9-5609-4de3-ac13-96fc83c7c99b.png">


## <span id="4">四、业务接入</span>
如果需要在你的业务中接入此项目，请参考[应用接入文档](./doc/APPLICATION.md)

## <span id="5">五、关于项目</span>

### <span id="51">1、项目文档</span>
项目在 [doc目录](./doc) 下提供了如下丰富完善的项目文档，以方便上手和使用。

- [APPLICATION](doc/APPLICATION.md) ：帮助你快速在业务中国接入此项目
- [HOWTOCODE](doc/HOWTOCODE.md) ：更深的了解项目，包括架构设计、底层原理
- [HELP](doc/HELP.md) ：解决安装和部署过程中问题的帮助手册，包括镜像制作帮助、容器部署帮助等
- 还有 [QUESTION](doc/QUESTION.md) / [CHANGELOGG](doc/CHANGELOG.md) / [CONTRIBUTING](doc/CONTRIBUTING.md) 等文档

### <span id="52">2、参与项目</span>
项目源码设计简单易懂，如有更好的想法，可参考[如何贡献](doc/CONTRIBUTING.md)文档，期待提出宝贵的 [Pull request](https://github.com/WGrape/esupdater/pulls)  。

如果在了解和使用过程中，有任何疑问，也欢迎提出宝贵的 [Issue](https://github.com/WGrape/esupdater/issues/new) 。

开源不易，如果支持本项目欢迎```star```，以激励维护和更新的动力。
