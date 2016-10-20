#!/bin/sh

# load common setting
cd $(dirname $0)
. ./shell_env

# delete the password file
if [ -e ./common.sh ]; then
    . ./common.sh
    rm_password_file
fi

# delete the scripts copied from the container
echo "Removing the scripts copied from the container."
for f in ${SCRIPTS[@]}
do
    echo -n "+$f: "
    if [ -e $f ]; then
	rm -rf $f
	echo "+Deleted."
    else
	echo "+Not found."
    fi
done

# delete the container
if docker ps -a | grep $CONTAINER; then
  echo Removing the containers...
  docker rm -f $CONTAINER
fi

