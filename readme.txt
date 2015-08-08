=======================
Setting ID and Password
=======================
Be sure to change IDs and passwords before using this docker image.

(1) phpmyadmin
The whole contents of phpmyadmin will be configured to be protect by Basic
Authentication. Change the user and password for Basic Authentication in
the script.

$ vi etc/phpmyadmin_basic_auth.sh

BASIC_AUTH_USER=phpadmin
BASIC_AUTH_PASSWD=mypasswd


(2) MySQL
Two users will be set up in MySQL. Change the user and password in the
script. (Note: you cannot change 'root' user's name.)

$ vi etc/setup_mysql.sh

# root password
MYSQL_ROOT_PASSWD=mypasswd

# administrator
MYSQL_ADMIN_USER=admin
MYSQL_ADMIN_PASSWD=mypasswd


(3) Fuelphp
Change the username and password. Be sure to set the user
who has privilages to access to the MySQL database.
$ vi etc/app/fuel/app/config/development/db.php

            'username'   => 'admin',
            'password'   => 'mypasswd',


(4) Build a new image
To enable the new credentials above, you should build a docker image.

$ ./docker-build.sh




=====================
Starting ctfscore app
=====================

The following instructions assume that
  - ctfscore app runs on a docker container.
  - all tasks should be done in a docker host machine.
  - current directory is the top level directory of the distributed file.
    (i.e. the directory that has Dockerfile)


(1) build image
Build a docker image if you want. Needed only the first time.
./docker-build.sh


(2) export app from container to localhost
./docker-export-vol.sh


(3) run the container
./docker-run-vol.sh


(4) app config
Change app config if you want.
$ vi mnt/app/fuel/app/config/ctfscore.php


(5) puzzles
Put puzzles in the specified directory.
See the following file for more details.
$ cat mnt/app/fuel/app/ctfadmin/puzzles/readme.txt


(6) flags
Write flag information in a file as php array syntax. Note that each flag
consists of flag_id, flag and point. See the following sample file.
$ vi mnt/app/fuel/app/ctfadmin/batch/flags.php

After saving the above file, insert flags into the DB.
$ ./ctfscore_insert_flags.sh


(7) start and end time
Write start and end time of CTF in a file as php array syntax. Note that
times should be written in MySQL DATETIME format. See the following sample
file.
$ vi mnt/app/fuel/app/ctfadmin/batch/times.php

After saving the above file, insert flags into the DB.
$ ./ctfscore_update_times.sh


(8) users [optional]
Basically each user should register by him/herself from the login page, so
just skip this section.
However if you want to prepare users and passwords, follow the instructions
bellow.

Write the pairs of username and password in a file as php array syntax.
$ vi mnt/app/fuel/app/ctfadmin/batch/users.php

After saving the above file, insert flags into the DB.
$ ./ctfscore_insert_users.sh


(9) test
Open a web browser and access the urls.
http://localhost/
http://localhost/mgmt/
http://localhost/phpmyadmin/

