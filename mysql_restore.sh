#!/bin/bash

source ./shell_env

if [ -z $1 ]; then
  echo "Usage: $(basename $0) file"
  exit 1
fi
file=$1

if ! ./mysql_ping.sh; then
  exit 1
fi
get_container_ip

# gunzip and cut the .gz extension if it's a gzip file
filetype=$(file $file | awk '{print $2}')
if [ $filetype = "gzip" ]; then
  gunzip $file
  file=$(echo $file | sed 's/\.gz$//')
fi

mysql -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $CONTAINER_IP < $file

if [ $? -eq 0 ]; then
  echo "Success. Restored from $file"
  exit 0
else
  exit 1
fi

