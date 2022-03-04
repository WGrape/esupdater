<div align="center" >
<img width="200" alt="img" src="https://user-images.githubusercontent.com/35942268/147061994-f0d5a3ec-2d5f-4d72-af1c-139289547f25.png">
</div>

<div align="center">
    <p>一个基于Canal实现ES文档增量更新的高性能轻量框架</p>
</div>

<p align="center">
    <img src="https://img.shields.io/badge/php-7.0+-blue.svg">
    <img src="https://img.shields.io/badge/release-v2.0.5-blue.svg">
    <img src="https://img.shields.io/badge/version-2.x-blue.svg">
    <img alt="Docker Pulls" src="https://img.shields.io/docker/pulls/lvsid/phpkafka">
    <a href="https://app.travis-ci.com/github/WGrape/esupdater"><img src="https://app.travis-ci.com/WGrape/esupdater.svg?branch=master"><a>
    <a href="https://wgrape.github.io/esupdater/report.html"><img src="https://img.shields.io/badge/unitest-100%25-yellow.svg"></a>
    <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-green.svg"></a>
    <a href="doc/HOWTOCODE.md"><img src="https://img.shields.io/badge/doc-中文-red.svg"></a>
</p>

- [一、介绍](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、轻量级框架](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、全面容器化](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、事件驱动化](#13)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、插件化扩展](#14)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、高性能消费](#15)
- [二、快速上手](#2)
- [三、业务接入](#3)
- [四、扩展列表](#4)
- [五、关于项目](#5)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、深入了解](#51)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、欢迎参与](#52)
- [六、贡献列表](#6)

## <span id="1">一、介绍</span>
ESUpdater是一个基于Canal实现ES文档增量更新的高性能轻量框架。基于以下优势，可以让你快速上手和使用。

<img width="900" alt="Architecture" src="https://user-images.githubusercontent.com/35942268/145793762-a23899d6-c162-4527-ae72-643edc80bb18.png">

### <span id="11">1、轻量级框架</span>
无论安装使用，还是代码设计，整个框架都非常轻量，优雅的完成数据二次处理和ES增量更新。

### <span id="12">2、全面容器化</span>
为解决各种依赖安装的复杂麻烦问题，已实现全面容器化，只需一条命令就可以轻松安装、部署、和维护。

### <span id="13">3、事件驱动化</span>
基于框架内部的事件驱动设计，可以轻松地注册不同数据表的变更事件和回调，优雅地实现增量更新。

### <span id="14">4、插件化扩展</span>
在不影响框架内部运行的前提下，支持插件化扩展，实现对内部行为的自定义扩展。

### <span id="15">5、高性能消费</span>
通过一个```Consumer```进程和多个```Worker```进程的一对多通信模型，最少提高10倍的吞吐量，实现高性能消费。

## <span id="2">二、快速上手</span>
> 预计只需要 **3分钟** 即可完成 ！

以下操作中会依赖Docker，所以请先安装并启动它。如果只是试用则强烈建议你全程使用<a href="https://labs.play-with-docker.com/">在线Docker网站</a>，按如下步骤安装即可，非常方便。

### <span id="21">1、获取项目</span>
通过```git clone```或下载Release包即可获取项目，如果出错请参考[获取过程帮助](doc/HELP.md#12)文档。

```bash
git clone https://github.com/WGrape/esupdater
cd esupdater
```

### <span id="22">2、开始安装</span>
执行```install```目录下的```install.sh```安装脚本时，需要传递如下参数以实现[设置环境变量](./doc/APPLICATION.md#3)。如果出错请参考[安装过程帮助](doc/HELP.md#13)文档。

- ```your_local_ip``` ：本机IP参数，通过```ifconfig```查看，通常为192.168开头，而不是127.0.0.1

```bash
cd install
bash install.sh ${your_local_ip}
cd ..
```

### <span id="24">3、运行项目</span>
安装成功后，执行根目录下的```start.sh```启动脚本即可。如果出错请参考[运行过程帮助](doc/HELP.md#3)文档。

```bash
bash start.sh

# 查看日志输出
tail -f /home/log/esupdater/debug.log.20220111
```

### <span id="25">4、测试运行</span>
在另一个窗口进入```kafkaContainer```容器中，按如下操作启动```Kafka生产者```

```bash
docker exec -it kafkaContainer /bin/bash
cd /opt/kafka/

# 启动时可能会出现warn, 忽略即可
./bin/kafka-console-producer.sh --broker-list localhost:9092 --topic default_topic
```

<img width="843" alt="img1" src="https://user-images.githubusercontent.com/35942268/148804272-b00483a9-3861-4aab-8b2f-aee963784694.png">

启动成功后会进入一个生产消息的命令行，发送任意消息后，查看上一步日志中的输出，如果出现如下类似日志则说明服务已经成功运行 ！

<img width="823" alt="img2" src="https://user-images.githubusercontent.com/35942268/148806227-25af15b9-5609-4de3-ac13-96fc83c7c99b.png">

## <span id="3">三、业务接入</span>
如果需要在你的业务中接入此项目，请参考[应用接入文档](./doc/APPLICATION.md)。

## <span id="4">四、扩展列表</span>
基于插件化扩展开发，项目提供了一系列开箱即用的扩展。

### 1、AutoGenerateCallback
一个自动生成```Handler```和```Service```的事件回调模块的扩展。具体使用见[使用介绍](./plugin/autogeneratecallback/README.md)


## <span id="5">五、关于项目</span>

### <span id="51">1、深入了解</span>
如果想要深入了解本项目，在 [doc目录](./doc) 下提供了如下丰富完善的项目文档，欢迎阅读。

- [APPLICATION](doc/APPLICATION.md) ：帮助你快速在业务中接入此项目
- [HOWTOCODE](doc/HOWTOCODE.md) ：更深的了解项目，包括架构设计、底层原理
- [HELP](doc/HELP.md) ：解决安装和部署过程中问题的帮助手册，包括镜像制作帮助、容器部署帮助等

### <span id="52">2、参与项目</span>
项目源码设计简单易懂，如有更好的想法，可参考[如何贡献](doc/CONTRIBUTING.md)文档，期待提出宝贵的 [Pull request](https://github.com/WGrape/esupdater/pulls)  。

如果在了解和使用过程中，有任何疑问，也欢迎提出宝贵的 [Issue](https://github.com/WGrape/esupdater/issues/new) 。

开源不易，如果支持本项目 **欢迎Star ！** 以激励维护和更新的动力。

## <span id="6">六、贡献列表</span>
所有对本项目有过重要贡献的用户，会收录在此贡献者列表中。

- 感谢 [sick-cat](https://github.com/sick-cat) 提出的Issue ：[启动配置](https://github.com/WGrape/esupdater/issues/41)
- 感谢 [onser3](https://github.com/onser3) 提出的Issue ：[自动生成handler和service层](https://github.com/WGrape/esupdater/issues/44)
