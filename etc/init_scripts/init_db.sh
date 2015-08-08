#!/bin/bash

# initialize application database
cd /app
php oil r ctfscore:create_database
php oil r ctfscore:init_all_tables

# sample user (username=testuser, password=testuser)
# should delete later
# php oil r ctfscore:insert_user 'testuser' 'testuser'

