## 一、架构介绍
ESUpdater支持的所有命令（```start```、```stop```、```work```等），在底层都是通过执行```/esupdater.php```入口文件完成

## 二、如何开发
ESUpdater的业务开发模式和```MVC```模式类似

- [1、创建应用目录](#1)
- [2、创建应用子目录](#2)
- [3、创建新的Handlers](#3)
- [4、附录-参考文档](#4)

### <span id="1">1、创建应用目录</span>
> 如果应用目录已存在，跳过此操作即可

在```app/```目录下，创建自己的应用目录，一般以业务名命名，如```app/user```

### <span id="2">2、创建应用子目录</span>
> 如果应用子目录已存在，跳过此操作即可

在```app/user/```目录下，分别创建```controllers```、```services```目录

### <span id="3">3、创建新的Handlers</span>
以```MVC```模式，分别创建```XXXController```、```XXXService```即可

### <span id="4">4、附录-参考文档</span>

- 有关```php-rdkafka```的配置可以 [参考文档](https://github.com/arnaud-lb/php-rdkafka)
- 有关```librdkafka```的配置可以 [参考文档](https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md)
- 有关```PHP Kafka```类的使用可以 [参考文档](https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-consumertopic.html)
