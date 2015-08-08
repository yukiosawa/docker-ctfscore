#!/bin/bash

# OPTION: basic authentication for phpmyadmin

BASIC_AUTH_USER=phpadmin
BASIC_AUTH_PASSWD=mypasswd
CONF=/etc/phpmyadmin/apache.conf

htpasswd -bc /etc/phpmyadmin/.htpasswd $BASIC_AUTH_USER $BASIC_AUTH_PASSWD

echo '<Directory /usr/share/phpmyadmin>' >> $CONF
echo '    AuthType Basic' >> $CONF
echo '    AuthName "Restricted Files"' >> $CONF
echo '    AuthUserFile /etc/phpmyadmin/.htpasswd' >> $CONF
echo '    Require valid-user' >> $CONF
echo '</Directory>' >> $CONF

