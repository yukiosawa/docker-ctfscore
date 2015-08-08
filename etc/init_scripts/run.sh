#!/bin/bash

# start redis on background
/usr/bin/redis-server &

# start nodejs on background
cd /app/nodejs && /usr/bin/node app.js > /var/log/nodejs.log &

# start apache2 daemon on background
source /etc/apache2/envvars
/usr/sbin/apache2 -D FOREGROUND &

# start mysql daemon on foreground
/usr/bin/mysqld_safe

