# Run stop.sh to prevent start repeatedly
bash stop.sh

# Pull without password
git config --global credential.helper store
git pull

# Build esupdater image
docker build -t esupdater .

# Run container
# docker run ---cpuset-cpus="0,1" --cpus=1.5 --cpuset-mems="2,3" --name {ContainerName} -d -v {LocalPath:ContainerPath} {imageName}
docker run --cpus=1.5 --name esupdaterContainer -d -v /home/log/esupdater/:/home/log/esupdater/ esupdater
if [ $? -ne 0 ]; then
  echo -e ">>>>>>>>Start failure(code=1)<<<<<<<<"
  exit 1
fi

# Exec command
docker exec -d esupdaterContainer php esupdater.php start
if [ $? -ne 0 ]; then
  echo -e ">>>>>>>>Start failure(code=2)<<<<<<<<"
  exit 1
else
  echo -e "========Start success========"
fi
