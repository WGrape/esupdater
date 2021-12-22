# Exec command: php esupdater.php stop
# Use exec command with -i argument would run and return result synchronously: docker exec -i esupdaterContainer php -r "var_dump(123);"
docker exec -i esupdaterContainer php esupdater.php stop

# Stop and remove container
docker stop esupdaterContainer
docker container rm esupdaterContainer

# Remove image
docker rmi esupdater

if [ $? -ne 0 ]; then
  echo "停止失败"
else
  echo "停止成功"
fi