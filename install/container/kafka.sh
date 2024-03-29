#!/usr/bin/env bash

# Pull images first.
docker pull wurstmeister/zookeeper
docker pull wurstmeister/kafka

# Start zookeeper.
containerCount=0
for file in $(docker container ls -f name=zookeeperContainer -q)
do
    ((containerCount++))
done
if [ $containerCount -ne 0 ]; then
  echo -e "Start zookeeperContainer already, is skipped."
else
  docker run -d --name zookeeperContainer -p 2181:2181 -t wurstmeister/zookeeper
fi

# Start kafka server.
# use ifconfig command to get the local ip: 192.168.x.x not 127.0.0.1
localIP=$(ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|grep '192.168' |awk '{print $2}'|tr -d "addr:")
containerCount=0
for file in $(docker container ls -f name=kafkaContainer -q)
do
    ((containerCount++))
done
if [ $containerCount -ne 0 ]; then
  echo -e "Start kafkaContainer already, is skipped."
else
  docker run -d --name kafkaContainer -p 9092:9092 -e KAFKA_BROKER_ID=0 -e KAFKA_ZOOKEEPER_CONNECT=${localIP}:2181 -e KAFKA_ADVERTISED_LISTENERS=PLAINTEXT://${localIP}:9092 -e KAFKA_LISTENERS=PLAINTEXT://0.0.0.0:9092 -t wurstmeister/kafka
fi

# Login kafka server container
# docker exec -it kafkaContainer /bin/bash
# cd /opt/kafka/
# ./bin/kafka-console-producer.sh --broker-list localhost:9092 --topic default_topic
# ./bin/kafka-console-consumer.sh --bootstrap-server localhost:9092 --topic default_topic --from-beginning
