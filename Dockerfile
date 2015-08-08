# Docker version 1.0.1
# CTF scoreboard application

FROM yuki/fuel:base

MAINTAINER yuki

ENV DEBIAN_FRONTEND noninteractive

# node.js and socket.io
RUN \
  echo deb http://ftp.jp.debian.org/debian/ wheezy-backports main >> /etc/apt/sources.list && \
  apt-get update && \
  apt-get install -y nodejs curl && \
  update-alternatives --install /usr/bin/node nodejs /usr/bin/nodejs 100 && \
  mkdir -p /app/nodejs && \
  cd /app/nodejs && \
  curl https://www.npmjs.com/install.sh | sh && \
  npm install socket.io && \
  cp /app/nodejs/node_modules/socket.io/node_modules/socket.io-client/socket.io.js /app/public/assets/js/.

# socket.io-php-emitter
RUN \
  apt-get update && \
  apt-get install -y redis-server && \
  cd /app/nodejs && \
  npm install socket.io-redis && \
  npm install socket.io-emitter && \
  git clone https://github.com/rase-/socket.io-php-emitter.git && \
  cd socket.io-php-emitter && \
  /app/composer.phar install

# sound on docker
RUN \
  apt-get install -y pulseaudio && \
  echo >> /etc/pulse/client.conf && \
  echo \# added for ctfscore >> /etc/pulse/client.conf && \
  echo default-server = $(ip route | grep default | cut -d' ' -f 3) >> /etc/pulse/client.conf

# init scripts
COPY etc/init_scripts/run.sh /init_scripts/
COPY etc/init_scripts/start_mysql.sh /init_scripts/
RUN chmod 755 /init_scripts/*.sh

# OPTION: phpmyadmin
RUN \
  /init_scripts/start_mysql.sh && \
  apt-get update && \
  apt-get install phpmyadmin -y && \
  ln -s ../../phpmyadmin/apache.conf /etc/apache2/conf.d/phpmyadmin.conf

# OPTION: basic authentication for phpmyadmin
COPY etc/init_scripts/phpmyadmin_basic_auth.sh /init_scripts/
RUN chmod +x /init_scripts/phpmyadmin_basic_auth.sh
RUN /init_scripts/phpmyadmin_basic_auth.sh

# mysql configuration
COPY etc/init_scripts/setup_mysql.sh /init_scripts/
RUN chmod 755 /init_scripts/setup_mysql.sh
RUN \
  sed -i -e"s/^bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/" /etc/mysql/my.cnf && \
  /init_scripts/start_mysql.sh && \
  /init_scripts/setup_mysql.sh

# CTF scoreboard app
COPY etc/app/. /app/
COPY etc/init_scripts /init_scripts
RUN chmod +x /init_scripts/*
RUN \
  /init_scripts/start_mysql.sh && \
  /init_scripts/init_db.sh

EXPOSE 80 3306 8080
CMD ["/init_scripts/run.sh"]

