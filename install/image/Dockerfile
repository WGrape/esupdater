# This dockerfile will make images of phpkafka.

# Must use version 7.0, or it would be error.
FROM php:7.0-fpm

# Use homeland image.
RUN mv /etc/apt/sources.list /etc/apt/sources.list.bak \
    && echo 'deb http://mirrors.163.com/debian/ stretch main non-free contrib' > /etc/apt/sources.list \
    && echo 'deb http://mirrors.163.com/debian/ stretch-updates main non-free contrib' >> /etc/apt/sources.list \
    && echo 'deb http://mirrors.163.com/debian-security/ stretch/updates main non-free contrib' >> /etc/apt/sources.list \
    && apt-get update

# Disable commandline interactive.
ENV DEBIAN_FRONTEND noninteractive

# Install git.
# -y is input yes automatically.
RUN apt-get update \
    && apt-get -y install git

# Install vim.
RUN apt-get update \
    && apt-get -y install vim

# Install composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Install pecl.
RUN apt-get update \
    && apt-get -y install autoconf \
    && apt-get -y install libz-dev

# Install libkakfa.
RUN apt-get update \
    && apt-get -y install librdkafka-dev=0.9.3-1


# Install the php kafka extension.
# Need librdkafka-dev=0.9.3-1 / rdkafka-3.0.0 this version will work, or it would be error.
# Check rdkafka versions: https://pecl.php.net/package/rdkafka
# Check librdkafka versions: apt search librdkafka-dev
RUN pecl channel-update pecl.php.net \
    && pecl install rdkafka-3.0.0 \
    && docker-php-ext-enable rdkafka

# Install and enable mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
