#!/bin/bash

source ./shell_env

# initialize the directory in which the app is being exported
if [ -e $MNT_DIR ]; then
  rm -rf $MNT_DIR
fi
mkdir -p $MNT_DIR
# assigning the proper SELinux policy type: for RHEL and CentOS
if ! ls -Zd $MNT_DIR | grep '?' > /dev/null; then
  chcon -Rt svirt_sandbox_file_t $MNT_DIR
fi

# copy app from container to host
docker run --rm -v $MNT_DIR:/mnt $IMAGE cp -pr /app /mnt/app

