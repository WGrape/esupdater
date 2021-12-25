# Exec command: php esupdater.php stop
# Use exec command with -i argument would run and return result synchronously: docker exec -i esupdaterContainer php -r "var_dump(123);"
docker exec -i esupdaterContainer php esupdater.php stop

# Stop and remove container
docker stop esupdaterContainer
docker container rm esupdaterContainer

# Remove image
docker rmi esupdater

if [ $? -ne 0 ]; then
  echo -e ">>>>>>>>Stop failure<<<<<<<<"
  exit 1
else
  echo -e "========Stop success========"
fi
