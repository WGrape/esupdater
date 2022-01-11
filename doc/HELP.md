### 目录
- [一、安装过程帮助](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、Git命令不存在](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、无法正常Clone](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、运行安装脚本出错](#13)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、Windows系统如何安装](#14)
- &nbsp;&nbsp;&nbsp;&nbsp;[5、out of capacity错误](#15)
- [二、镜像制作帮助](#2)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、Docker命令不存在](#21)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、无法连接Docker](#22)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、pecl.php.net更新失败](#23)
- [三、容器部署帮助](#3)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、phpkafka镜像不存在](#31)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、/home/log/esupdater/目录不存在或无权限写](#32)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、KafkaConsumer创建失败](#33)
- &nbsp;&nbsp;&nbsp;&nbsp;[4、Consumer highLevelConsuming fetch timeout](#34)
- [四、版本选择](#4)

## <span id="1">一、安装过程帮助</span>
请先通过 ```git clone``` 或 [下载Release包](https://github.com/WGrape/esupdater/releases) 的方式获取项目

### <span id="11">1、Git命令不存在</span>
检查git是否已正常安装，查看 [如何安装Git](https://git-scm.com/book/zh/v2/%E8%B5%B7%E6%AD%A5-%E5%AE%89%E8%A3%85-Git)

### <span id="12">2、无法正常Clone</span>
针对于常见无法正常Clone的问题，有如下几种解决方案

- 尝试使用```https```方式进行clone
- 检查网络和网速是否正常，或使用 [esupdater国内版仓库](https://gitee.com/WGrape/esupdater)

### <span id="13">3、运行安装脚本出错</span>
如果获取项目已经成功，但是在运行```install.sh```安装脚本阶段出错的话，有如下几种解决方案

- 制作镜像过程出错 ：参考 [镜像制作帮助](#2) 文档

- 提示```Error response from daemon: Get "https://registry-1.docker.io/v2/": EOF``` 错误 ：检查网络连接，关闭网络代理即可

### <span id="14">4、Windows系统如何安装</span>
目前暂不支持直接在Windows系统上操作，可以选择在Linux虚拟机、Docker环境中安装，如使用 <a href="https://labs.play-with-docker.com/">在线Docker网站</a>

### <span id="15">5、out of capacity错误</span>

We are really sorry but we are out of capacity and cannot create your session at the moment. Please try again later.

访问 ```https://labs.play-with-docker.com/``` 时如果出现上述错误，暂无解决方案，需要多尝试几次。


## <span id="2">二、镜像制作帮助</span>
在```install/image```目录中已提供了开箱可用的```phpkafka```镜像文件，只需要简单的执行```bash make.sh```命令即可快速生成```phpkafka```镜像。

自带的```install/image/Dockerfile```镜像文件，已经过多台Unix机器上的多次测试，均可以顺利的成功制作。但是不排除在特殊情况下会存在制作失败的情况，下面会总结出常见的错误和解决方案。

### <span id="21">1、Docker命令不存在</span>
安装镜像必须依赖于```Docker```，所以请务必成功安装```Docker```，否则无法创建镜像。

### <span id="22">2、无法连接Docker</span>

#### (1) 错误提示
```text
Cannot connect to the Docker daemon at unix:///var/run/docker.sock. Is the docker daemon running?
```

#### (2) 错误原因
本地Docker服务未启动

#### (3) 解决方案
开启本地Docker服务即可

### <span id="23">3、pecl.php.net更新失败</span>

#### (1) 错误提示
```text
Updating channel "pecl.php.net"
Channel "pecl.php.net" is not responding over http://, failed with message: File http://pecl.php.net:80/channel.xml not valid (redirected but no location)
Trying channel "pecl.php.net" over https:// instead
Cannot retrieve channel.xml for channel "pecl.php.net" (File https://pecl.php.net:443/channel.xml not valid (redirected but no location))
```

#### (2) 错误原因
网络异常，无法正常连接```pecl.php.net```

#### (3) 解决方案
检查网络是否正常或关掉网络代理

## <span id="3">三、容器部署帮助</span>

### <span id="31">1、phpkafka镜像不存在</span>
> pull access denied for phpkafka, repository does not exist ... ...

出现这种错误是因为跳过了安装步骤，直接执行部署操作导致的。

由于容器化部署方案依赖于```phpkafka```镜像，所以如果提示此镜像不存在，请先参考[快速使用-开始安装](../README.md#22)文档执行安装操作，或直接手动执行```cd image && bash make.sh```完成镜像的制作。

### <span id="32">2、/home/log/esupdater/目录不存在或无权限写</span>
由于容器默认会把目录挂载到宿主机的 ```/home/log/esupdater/``` 相同目录下，所以请确保宿主机有此目录和写入权限

或者也可以选择修改[容器的运行时配置](APPLICATION.md#32)中的```目录挂载```，修改方式如下

```bash
vi start.sh

# 替换以下内容
docker run --cpus=1.5 --name esupdaterContainer -d -v {你的宿主机目录}:/home/log/esupdater/ esupdater
```

### <span id="33">3、KafkaConsumer创建失败</span>
> Consumer failed to new KafkaConsumer: "group.id" must be configured

如果在```fatal.log```中出现```KafkaConsumer```创建失败的报错，请检查```consumer.php```中的```kafka```服务配置是否可以正常连接

### <span id="34">4、Consumer highLevelConsuming fetch timeout</span>
重新启动后可能会报一段时间的```Consumer highLevelConsuming fetch timeout```问题，持续约为2~5秒。

原因 ：重启后需要重新连接```kafka```消费数据，在第一次连接时需要建立TCP和一些额外资源等，所以导致耗时相对较长。

## <span id="4">四、版本选择</span>

项目版本号规则为```主版本```-```次版本```-```修订号```，其中主版本主要做重大功能升级，次版本主要做性能和功能优化，修订号则做问题修复和完善。

所以```次版本```和```修订号```建议选择最新稳定版本的 [Release包](https://github.com/WGrape/esupdater/releases) ，```主版本```则根据以下对比信息选择合适的即可，可以查看更详细的 [版本对比](doc/CONTRIBUTING.md#5) 信息。

| 主版本号 | Composer |
| --- | :----:  |
| v1.x | 不支持 |
| v2.x | 支持 |