<?php
/**
 * The development database settings. These get merged with the global settings.
 */

return array(
    'default' => array(
	'type' => 'mysqli',
	'connection'  => array(
	    /* 'dsn'        => 'mysql:host=localhost;dbname=ctf_score', */
	    'hostname'   => 'localhost',
	    'port'       => '3306',
	    'database'   => 'ctf_score',
	    'username'   => 'admin',
	    'password'   => 'mypasswd',
	),
    ),
);
