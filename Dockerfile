FROM harbor.changba.io/inf/php:7.2-fpm

# 安装PHP扩展

RUN git clone https://github.com/edenhill/librdkafka.git \
    cd librdkafka \
    ./configure \
    make && make install

RUN git clone https://github.com/arnaud-lb/php-rdkafka.git \
    cd php-rdkafka \
    phpize \
    ./configure --with-php-config=/usr/bin/php-config \
    make && make install

FROM scratch
WORKDIR /dist

CMD ["php", "/dist/esupdater.php", "start"]
