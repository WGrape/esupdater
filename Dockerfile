FROM phpkafka
# If you failed to install phpkafka, you can the lvsid/phpkafka:v1.0 in dockerhub: https://hub.docker.com/repository/docker/lvsid/phpkafka
# FROM lvsid/phpkafka:v1.0

WORKDIR /dist
COPY . /dist/
RUN mkdir -p /home/log/esupdater \
   && composer install --quiet

# Do not run start command here, because it means the container is equal consumer process,
# once the consumer was stopped, the container would exit,
# so the workers would not stopped safely.
# CMD ["php", "/dist/esupdater.php", "start"]
