#!/bin/bash

# insert ctf users
cd /app
php oil r ctfscore:insert_users ./fuel/app/ctfadmin/batch/users.php

