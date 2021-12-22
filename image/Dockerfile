# 生成phpkafka镜像的dockerfile

# 必须7.0不然会报错
FROM php:7.0-fpm

# 更新为国内镜像
RUN mv /etc/apt/sources.list /etc/apt/sources.list.bak \
    && echo 'deb http://mirrors.163.com/debian/ stretch main non-free contrib' > /etc/apt/sources.list \
    && echo 'deb http://mirrors.163.com/debian/ stretch-updates main non-free contrib' >> /etc/apt/sources.list \
    && echo 'deb http://mirrors.163.com/debian-security/ stretch/updates main non-free contrib' >> /etc/apt/sources.list \
    && apt-get update

# 不开启命令行交互
ENV DEBIAN_FRONTEND noninteractive

# 安装git
# -y 输入yes
RUN apt-get update \
    && apt-get -y install git

# 安装Vim
RUN apt-get update \
    && apt-get -y install vim

# 安装pecl
RUN apt-get update \
    && apt-get -y install autoconf \
    && apt-get -y install libz-dev

# 安装Libkakfa
RUN apt-get update \
    && apt-get -y install librdkafka-dev=0.9.3-1


# 安装PHP的kafka扩展
# 需要安装librdkafka-dev=0.9.3-1 / rdkafka-3.0.0这种版本才可正常运行, 其他版本大多都不能成功
# 查看rdkafka版本: https://pecl.php.net/package/rdkafka
# 查看librdkafka版本: apt search librdkafka-dev
RUN pecl channel-update pecl.php.net \
    && pecl install rdkafka-3.0.0 \
    && docker-php-ext-enable rdkafka
