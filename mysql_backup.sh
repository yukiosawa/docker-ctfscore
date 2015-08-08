#!/bin/bash

source ./shell_env

if ! ./mysql_ping.sh; then
  exit 1
fi
get_container_ip

if [ ! -e $MYSQL_BKUP_DIR ]; then
  mkdir -p $MYSQL_BKUP_DIR
fi

# gzip a dump file to save disk space
mysqldump -u $MYSQL_ADMIN_USER -p$MYSQL_ADMIN_PASSWD -h $CONTAINER_IP $MYSQL_DUMP_OPT | gzip > $MYSQL_BKUP_DIR/$MYSQL_BKUP_FILE

if [ $? -ne 0 ]; then
  exit 1
fi

# change the permittion so that others can't read it
chmod 700 $MYSQL_BKUP_DIR/$MYSQL_BKUP_FILE

if [ $? -eq 0 ]; then
  echo "Success. Saved as $MYSQL_BKUP_DIR/$MYSQL_BKUP_FILE"
  exit 0
else
  exit 1
fi

