# Run stop.sh to prevent start repeatedly
bash stop.sh

# Pull without password
git config --global credential.helper store
git pull

# Build esupdater image
docker build -t esupdater .

# Run container
# docker run --name {ContainerName} -d -v {LocalPath:ContainerPath} {imageName}
docker run --name esupdaterContainer -d -v /home/log/docker/esupdater/:/home/log/esupdater/ esupdater
if [ $? -ne 0 ]; then
  echo "启动失败"
else
  echo "启动成功"
fi

# Exec command
docker exec -d esupdaterContainer php esupdater.php start
