#!/bin/bash

# update ctf start and end times
cd /app
php oil r ctfscore:update_times ./fuel/app/ctfadmin/batch/times.php

