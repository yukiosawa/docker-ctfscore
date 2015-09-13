#!/bin/bash

# insert ctf puzzles
cd /app
php oil r ctfscore:insert_puzzles ./fuel/app/ctfadmin/batch/puzzles.php

