#!/bin/bash

# load common setting
cd $(dirname $0)
. ./shell_env

# delete unused images.
echo Removing unused images ...
ImageIDs=$(docker images -f "dangling=true" -q)
if [ -z $ImageIDs ]; then
    echo No unused images found.
    exit
fi
docker rmi $ImageIDs
