#!/bin/bash

# insert ctf flags
cd /app
php oil r ctfscore:insert_flags ./fuel/app/ctfadmin/batch/flags.php

