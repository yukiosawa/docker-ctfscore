#!/bin/sh

# load common setting
cd $(dirname $0)
. ./shell_env

echo "Building a new image: $IMAGE"
docker build -t $IMAGE .

