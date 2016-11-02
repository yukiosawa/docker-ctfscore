#!/bin/bash

# load common setting
cd $(dirname $0)
. ./shell_env

# delete stopped containers
echo Removing stopped containers ...
docker rm $(docker ps -a -q)

