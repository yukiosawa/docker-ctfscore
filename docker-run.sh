#!/bin/bash

source ./shell_env

# delete the previous container
./docker-rm.sh

# run a new container
docker run -d --name $CONTAINER -p 80:80 -p 8080:8080 -p 3306:3306 $IMAGE

# enable sound from docker containers
./setup_pulse.sh

