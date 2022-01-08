#!/usr/bin/env bash
# Prevent start repeatedly
containerCount=0
for file in $(docker container ls -f name=esupdaterContainer -q)
do
    ((containerCount++))
done
if [ $containerCount -ne 0 ]; then
  echo -e ">>>>>>>>Start failure: please run stop.sh first<<<<<<<<"
  exit 1
fi

# Pull without password
git config --global credential.helper store
git pull

# Build esupdater image
docker build -t esupdater .
if [ $? -ne 0 ]; then
  echo -e ">>>>>>>>Start failure: failed to build<<<<<<<<"
  exit 1
fi

# Run container
# docker run --cpuset-cpus="0,1" --cpus=1.5 --cpuset-mems="2,3" --name {ContainerName} -d -v {LocalPath:ContainerPath} {imageName}
docker run --cpus=1.5 --name esupdaterContainer -d -v /home/log/esupdater/:/home/log/esupdater/ esupdater
if [ $? -ne 0 ]; then
  echo -e ">>>>>>>>Start failure: failed to run<<<<<<<<"
  exit 1
fi

# Exec command
docker exec -d esupdaterContainer php esupdater.php start
if [ $? -ne 0 ]; then
  echo -e ">>>>>>>>Start failure: failed to exec<<<<<<<<"
  exit 1
else
  echo -e "========Start success========"
fi
