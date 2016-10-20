#!/bin/sh

# load common setting
cd $(dirname $0)
. ./shell_env

# delete the previous container
./docker-rm.sh

# run a new container
echo "Running a new container: $CONTAINER (image: $IMAGE)"
docker run -d --name $CONTAINER -p 80:80 -p 8080:8080 -p 3306:3306 $IMAGE

# copy scripts from the container
echo "Copying scripts from the container."
APP=$(grep "ENV APP_DIR" ./Dockerfile | cut -d" " -f3)
for f in ${SCRIPTS[@]}
do
    echo "+$f"
    docker cp $CONTAINER:$APP/etc/scripts/$f .
done

# container log
echo
echo "####################################################################"
echo "# Waiting for the container starts the services ... (10 sec)       #"
echo "# After checking the log shows that the services have successfully #"
echo "# started, press Ctrl-C to return back to the terminal.            #"
echo "# Note that the container continues to run even if you hit Ctrl-C. #"
echo "# Enjoy!                                                           #"
echo "####################################################################"
echo
sleep 10s
# save the mysql credentials that have been generated in the container
. ./common.sh
docker logs $CONTAINER |egrep "MYSQL_ROOT_PASSWD|MYSQL_ADMIN_USER|MYSQL_ADMIN_PASSWD" > $PASSFILE
echo "Saved a password file: $PASSFILE"
docker logs -f $CONTAINER

