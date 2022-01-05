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
# docker tag {imageId} {host}:{version}
# docker push {host}:{version}
