#!/bin/bash

# root password
MYSQL_ROOT_PASSWD=mypasswd

# administrator
MYSQL_ADMIN_USER=admin
MYSQL_ADMIN_PASSWD=mypasswd

# allow administrator to access via network
mysql -e "GRANT ALL PRIVILEGES ON *.* TO $MYSQL_ADMIN_USER@'%' IDENTIFIED BY '$MYSQL_ADMIN_PASSWD' WITH GRANT OPTION;"

# set root password
mysqladmin password $MYSQL_ROOT_PASSWD -u root

