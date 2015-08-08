#!/bin/bash

source ./shell_env

if ! ./mysql_ping.sh; then
  exit 1
fi
get_container_ip

mysql -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $CONTAINER_IP

