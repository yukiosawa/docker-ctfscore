# Docker version 17.05.0-ce
# CTF scoreboard application for raspbian OS

# Choose one of these based on your host OS (x86/x64 or Raspberry pi)
FROM debian:jessie
#FROM resin/rpi-raspbian:jessie

MAINTAINER yuki

ENV APP_DIR /var/www/ctfscore
ENV DEBIAN_FRONTEND noninteractive

# timezone
RUN echo "Asia/Tokyo" > /etc/timezone && dpkg-reconfigure -f noninteractive tzdata

# sources.list
RUN echo "deb http://ftp.jp.debian.org/debian/ jessie main contrib non-free" > /etc/apt/sources.list && \
    echo "deb http://ftp.jp.debian.org/debian jessie-updates main contrib" >> /etc/apt/sources.list

# update
RUN apt-get update && apt-get -y upgrade

# utilities
RUN apt-get update && apt-get install -y \
    git \
    curl

# apache, mysql and php
RUN apt-get update && apt-get install -y \
    apache2 \
    mysql-server \
    php5 \
    php5-mysql

# fuelphp
RUN curl https://get.fuelphp.com:443/oil | sh && \
    oil create ctfscore && \
    cd ctfscore && \
    oil refine install && \
    cd .. && \
    rm ctfscore/public/favicon.ico && \
    mv ctfscore /var/www/. && \
    a2enmod rewrite

# node.js and socket.io
RUN apt-get update && apt-get install -y nodejs npm && \
    npm install n -g && \
    n stable && \
    apt-get purge -y nodejs npm && \
    /usr/local/bin/npm install socket.io && \
    find node_modules -name socket.io.js | xargs -i cp -p {} $APP_DIR/public/assets/js/.

# socket.io-php-emitter
RUN apt-get update && apt-get install -y redis-server && \
    /usr/local/bin/npm install socket.io-redis && \
    /usr/local/bin/npm install socket.io-emitter && \
    git clone https://github.com/ashiina/socket.io-php-emitter.git && \
    cd socket.io-php-emitter && \
    $APP_DIR/composer.phar install && \
    cd .. && \
    mkdir -p $APP_DIR/nodejs && \
    cp -r node_modules $APP_DIR/nodejs/. && \
    cp -r socket.io-php-emitter $APP_DIR/nodejs/.

# supervisor
RUN apt-get update && apt-get install -y supervisor
# RUN mkdir -p /var/log/supervisor

# CTF scoreboard app
RUN git clone https://github.com/yukiosawa/ctfscore.git && \
    cp -r ctfscore/etc/fuelphp/* $APP_DIR/. && \
    cp -r ctfscore/* $APP_DIR/.
RUN chown -R www-data:www-data $APP_DIR

COPY container-entrypoint.sh $APP_DIR/etc/scripts/.
 RUN chmod 755 $APP_DIR/etc/scripts/*.sh

# mysql configuration
RUN sed -i -e"s/^bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" /etc/mysql/my.cnf

# virtual host configuration
RUN cp ctfscore/etc/apache2/ctfscore.conf /etc/apache2/sites-available/. && \
    a2dissite 000-default && \
    a2ensite ctfscore

# supervisor configuration
RUN cp ctfscore/etc/supervisor/supervisord.conf /etc/supervisor/conf.d/

# don't show error messagess
RUN sed -i -e"s/^\# SetEnv FUEL_ENV production/SetEnv FUEL_ENV production/" $APP_DIR/public/.htaccess && \
    sed -i -e"s/^error_reporting(-1)/error_reporting(0)/" $APP_DIR/public/index.php && \
    sed -i -e"s/^ini_set('display_errors', 1)/ini_set('display_errors', 0)/" $APP_DIR/public/index.php

EXPOSE 80 3306 8080
CMD $APP_DIR/etc/scripts/container-entrypoint.sh
