#!/bin/bash

# start mysql daemon on background
mysqld_safe &

# wait until mysqld starts
mysqladmin --silent --wait=30 ping || exit 1

