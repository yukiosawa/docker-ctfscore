#!/bin/bash

if [ -z $1 ]; then
  echo "Usage: sudo $(basename $0) file1 [file2] [file3]"
  exit 1
fi

# load common setting
cd $(dirname $0)
. ./shell_env

echo "Restoring contents files into the container."

FILES=$@
APP=$(grep "ENV APP_DIR" ./Dockerfile | cut -d" " -f3)
FILE_BKUP_DIR=$APP/etc/scripts/backup/files

for f in ${FILES[@]}
do
    filename=$(basename $f)
    echo "---> $filename"
    docker exec $CONTAINER mkdir -p $FILE_BKUP_DIR
    docker cp $f $CONTAINER:$FILE_BKUP_DIR/$filename
    docker exec $CONTAINER $APP/etc/scripts/files_restore.sh $FILE_BKUP_DIR/$filename
done

echo "+Done."

