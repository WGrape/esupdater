FROM php:7.1.19-fpm

# 更新为国内镜像
RUN mv /etc/apt/sources.list /etc/apt/sources.list.bak \
    && echo 'deb http://mirrors.163.com/debian/ stretch main non-free contrib' > /etc/apt/sources.list \
    && echo 'deb http://mirrors.163.com/debian/ stretch-updates main non-free contrib' >> /etc/apt/sources.list \
    && echo 'deb http://mirrors.163.com/debian-security/ stretch/updates main non-free contrib' >> /etc/apt/sources.list \
    && apt-get update

# 开启rdkafka扩展, 在php镜像上做, 这样不会报不存在docker-php-ext-enable命令的错误
RUN docker-php-ext-enable rdkafka

FROM ubuntu:20.04

# 不开启命令行交互
ENV DEBIAN_FRONTEND noninteractive

# 安装git
# -y 输入yes
RUN apt-get update \
    && apt-get -y install git

# 安装pecl
RUN apt-get update \
    && apt-get -y install autoconf \
    && apt-get -y install libz-dev \
    && apt-get -y install software-properties-common \
    && apt-get update \
    && add-apt-repository ppa:ondrej/php \
    && apt-get -y install php7.0-dev \
    && apt-get -y install php-pear \
    && apt-get -y install php-xml php7.0-xml

# 安装Libkakfa
RUN apt-get update \
    && apt-get -y install librdkafka-dev

# 安装PHP的kafka扩展
RUN pecl channel-update pecl.php.net \
    && pecl install rdkafka-5.0.0 \

# updater项目
WORKDIR /dist
COPY . /dist/
# CMD ["php", "/dist/esupdater.php", "start"]
