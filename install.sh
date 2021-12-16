docker build -t esupdater .
docker images
docker run -t -i esupdater /bin/bash

echo "安装成功"