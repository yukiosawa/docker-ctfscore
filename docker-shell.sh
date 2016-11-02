#!/bin/bash

# load common setting
cd $(dirname $0)
. ./shell_env

docker exec -it $CONTAINER bash

