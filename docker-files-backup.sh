#!/bin/bash

# load common setting
cd $(dirname $0)
. ./shell_env

echo "Copying contents files from the container."

APP=$(grep "ENV APP_DIR" ./Dockerfile | cut -d" " -f3)

FILES=$(docker exec $CONTAINER $APP/etc/scripts/files_backup.sh | grep "+Done. Saved "|cut -d" " -f6)

FILE_BKUP_DIR=backup/files

if [ ! -e $FILE_BKUP_DIR ]; then
  mkdir -p $FILE_BKUP_DIR
fi

for f in ${FILES[@]}
do
    docker cp $CONTAINER:$f $FILE_BKUP_DIR
done

echo "+Done. Saved into $(pwd)/$FILE_BKUP_DIR"

