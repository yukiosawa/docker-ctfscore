#!/bin/bash

source ./shell_env

docker_nsenter $CONTAINER /init_scripts/update_times.sh

