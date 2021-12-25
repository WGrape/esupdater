## 镜像制作帮助
自带的```/image/Dockerfile```镜像文件，已经过多台Unix机器上的多次测试，均可以顺利的成功制作。但是不排除在特殊情况下会存在制作失败的情况，下面会总结出常见的错误和解决方案。

### 1、Docker命令不存在
安装镜像必须依赖于```Docker```，所以请务必成功安装```Docker```，否则无法创建镜像

### 2、无法连接Docker

#### (1) 错误提示
```text
Cannot connect to the Docker daemon at unix:///var/run/docker.sock. Is the docker daemon running?
```

#### (2) 错误原因
本地Docker服务未启动

#### (3) 解决方案
开启本地Docker服务即可

### 3、pecl.php.net更新失败

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
