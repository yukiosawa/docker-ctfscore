#!/bin/sh

echo '--------------------------------------------------------------'
echo 'Configure pulseaudio on host to listen from docker containers.'
echo '--------------------------------------------------------------'

if [ ! -e ~/.pulse/default.pa ]; then
  echo 'Copying default.pa for pulseaudio. [~/.pulse/default.pa]'
  if [ ! -e ~/.pulse ]; then
    mkdir ~/.pulse
  fi
  cp /etc/pulse/default.pa ~/.pulse/
else
  echo 'Already exists. [~/.pulse/default.pa]'
fi

if grep 'added for ctfscore' ~/.pulse/default.pa > /dev/null; then
  echo 'Already be configured for docker containers. [~/.pulse/default.pa]'
else
  echo 'Configuring to listen from docker containers. [~/.pulse/default.pa]'
  echo >> ~/.pulse/default.pa
  echo \# added for ctfscore >> ~/.pulse/default.pa
  echo load-module module-native-protocol-tcp auth-ip-acl=$(ip addr show docker0|grep 'inet ' | sed -e 's/^ *//' | cut -d' ' -f 2)\; >> ~/.pulse/default.pa
  # restart pulse audio
  pulseaudio -k
fi


