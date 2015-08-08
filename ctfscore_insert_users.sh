#!/bin/bash

source ./shell_env

docker_nsenter $CONTAINER /init_scripts/insert_users.sh

