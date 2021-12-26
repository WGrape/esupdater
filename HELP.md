### 目录
- [一、镜像制作帮助](#1)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、Docker命令不存在](#11)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、无法连接Docker](#12)
- &nbsp;&nbsp;&nbsp;&nbsp;[3、pecl.php.net更新失败](#13)
- [二、容器部署帮助](#2)
- &nbsp;&nbsp;&nbsp;&nbsp;[1、phpkafka镜像不存在](#21)
- &nbsp;&nbsp;&nbsp;&nbsp;[2、/home/log/esupdater/目录不存在或无权限写](#22)

## <span id="1">一、镜像制作帮助</span>
在```/esupdater/image```目录中已提供了开箱可用的```phpkafka```镜像文件，只需要简单的执行```bash make.sh```命令即可快速生成```phpkafka```镜像。

自带的```/image/Dockerfile```镜像文件，已经过多台Unix机器上的多次测试，均可以顺利的成功制作。但是不排除在特殊情况下会存在制作失败的情况，下面会总结出常见的错误和解决方案。

### <span id="11">1、Docker命令不存在</span>
安装镜像必须依赖于```Docker```，所以请务必成功安装```Docker```，否则无法创建镜像。

### <span id="12">2、无法连接Docker</span>

#### (1) 错误提示
```text
Cannot connect to the Docker daemon at unix:///var/run/docker.sock. Is the docker daemon running?
```

#### (2) 错误原因
本地Docker服务未启动

#### (3) 解决方案
开启本地Docker服务即可

### <span id="13">3、pecl.php.net更新失败</span>

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

## <span id="2">二、容器部署帮助</span>

### <span id="21">1、phpkafka镜像不存在</span>
容器化部署方案依赖于```phpkafka```镜像，如果提示此镜像不存在，参考[安装依赖](./README.md#22)文档，只需要一条命令就可以快速制作镜像

### <span id="22">2、/home/log/esupdater/目录不存在或无权限写</span>
由于容器默认会把目录挂载到宿主机的 ```/home/log/esupdater/``` 相同目录下，所以请确保宿主机有此目录和写入权限

或者也可以选择修改[容器的运行时配置](./README.md#32)中的```目录挂载```，修改方式如下

```bash
vi start.sh

# 替换以下内容
docker run --cpus=1.5 --name esupdaterContainer -d -v {你的宿主机目录}:/home/log/esupdater/ esupdater
```
