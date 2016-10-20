#!/bin/sh

# load common setting
cd $(dirname $0)
. ./shell_env

if [ -z $(docker ps -f name=$CONTAINER --format "{{.Names}}") ]; then
    echo "Container[$CONTAINER] is not running."
    exit 1
fi

COMMAND=$1
sudo nsenter --mount --uts --ipc --net --pid --target $(docker inspect --format '{{.State.Pid}}' $CONTAINER) -- $COMMAND

