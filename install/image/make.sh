#!/usr/bin/env bash
docker build -t phpkafka .
if [ $? -ne 0 ]; then
  echo -e ""
  echo -e ">>>>>>>>Make image failure<<<<<<<<"
  exit 1
else
  echo -e ""
  echo -e "========Make image success========"
  docker images
fi

# Push to docker repository
# docker login
# input your name and password
# docker tag adf2495d561e lvsid/phpkafka:v1.0
# docker push lvsid/phpkafka:v1.0
