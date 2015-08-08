#!/bin/bash

source ./shell_env

# delete the container
if docker ps -a | grep $CONTAINER; then
  echo Removing the containers...
  docker rm -f $CONTAINER
fi


