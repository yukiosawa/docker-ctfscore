#!/bin/sh

# Starts services in a docker container.
# Should be run in the container.

echo "Starting the initial processes: $0"

# load common setting
. $(dirname $0)/common.sh

# start mysqld
echo "Starting mysqld for the initial setting."
/usr/bin/mysqld_safe &
/usr/bin/mysqladmin --silent --wait=30 ping

# initialize the db
rm_password_file
$DIR/setup_mysql.sh
$DIR/init_app_db.sh

# stop mysqld
PID=$(cat /var/run/mysqld/mysqld.pid)
echo "Stopping mysqld: pid=$PID"
kill -TERM $PID
sleep 5s

# start services
echo "Starting the services by Supervisor."
/usr/bin/supervisord

