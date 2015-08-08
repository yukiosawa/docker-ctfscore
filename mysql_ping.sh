#!/bin/bash

source ./shell_env

if ! get_container_ip; then
  exit 1
fi

mysqladmin -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $CONTAINER_IP ping || exit 1

exit 0

