<?php

namespace Fuel\Tasks;

class Ctfscore
{

    public function create_database($args = NULL)
    {
	$this->_do_database("create");
    }


    public function delete_database($args = NULL)
    {
	$this->_do_database("delete");
    }


    private function _do_database($act = NULL)
    {
	/* read config parameters from db.php */
	\Config::load('db', true);
	$active = \Config::get('db.active');
	if (! $database = \Config::get('db.'.$active.'.connection.database'))
	{
	    echo "Failed to get a database name from db.php file.\n";
	    return;
	}

	/* error occurs when the database doesn't exist,
	   so clear the database name in the config */
	\Config::set('db.'.$active.'.connection.database', '');

	if ($act == "create") {
	    \DBUtil::create_database($database);
	    echo "Database created: ".$database."\n";
	}
	elseif ($act == "delete") {
	    \DBUtil::drop_database($database);
	    echo "Database deleted: ".$database."\n";
	}
    }


    public function init_all_tables()
    {
	/* delete all tables */
	$this->delete_reviews_table();
	$this->delete_gained_table();
        $this->delete_history_table();
	$this->delete_users_table();
	$this->delete_flags_table();
	$this->delete_times_table();
	/* create all tables */
	$this->create_users_table();
	$this->create_flags_table();
	$this->create_times_table();
	$this->create_gained_table();
        $this->create_history_table();
	$this->create_reviews_table();
    }


    public function create_users_table()
    {
	// get the tablename
	\Config::load('simpleauth', true);
	$table = \Config::get('simpleauth.table_name', 'users');

	// make sure the configured DB is used
	\DBUtil::set_connection(\Config::get('simpleauth.db_connection', null));

	// only do this if it doesn't exist yet
	if (\DBUtil::table_exists($table))
	{
	    return;
	}
	// table users
	\DBUtil::create_table($table, array(
	    'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
	    'username' => array('type' => 'varchar', 'constraint' => 50),
	    'password' => array('type' => 'varchar', 'constraint' => 255),
	    'group' => array('type' => 'int', 'constraint' => 11, 'default' => 1),
	    'email' => array('type' => 'varchar', 'constraint' => 255),
	    'last_login' => array('type' => 'varchar', 'constraint' => 25),
	    'login_hash' => array('type' => 'varchar', 'constraint' => 255),
	    'profile_fields' => array('type' => 'text'),
	    'created_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
	    'updated_at' => array('type' => 'int', 'constraint' => 11, 'default' => 0),
	    'totalpoint' => array('type' => 'int', 'default' => 0),
	    'pointupdated_at' => array('type' => 'datetime'),
	), array('id'));

	// reset any DBUtil connection set
	\DBUtil::set_connection(null);

	echo "Table created: ".$table."\n";
    }


    public function delete_users_table()
    {
	// get the tablename
	\Config::load('simpleauth', true);
	$table = \Config::get('simpleauth.table_name', 'users');

	// make sure the configured DB is used
	\DBUtil::set_connection(\Config::get('simpleauth.db_connection', null));

	// drop the admin_users table
	\DBUtil::drop_table($table);

	// reset any DBUtil connection set
	\DBUtil::set_connection(null);

	echo "Table deleted: ".$table."\n";
    }


    public function create_flags_table()
    {
        $table = 'flags';
	
	// only do this if it doesn't exist yet
	if (\DBUtil::table_exists($table))
	{
	    return;
	}
        \DBUtil::create_table($table, array(
	    'id' => array('type' => 'int', 'auto_increment' => true),
            'flag_id' => array('type' => 'int'),
	    'flag' => array('type' => 'varchar', 'constraint' => 255),
	    'point' => array('type' => 'int'),
	    'bonus_point' => array('type' => 'int'),
        ), array('id'));

	echo "Table created: ".$table."\n";
    }


    public function delete_flags_table()
    {
        $table = 'flags';
	\DBUtil::drop_table($table);
	echo "Table deleted: ".$table."\n";
    }


    public function create_gained_table()
    {
	$table = 'gained';

	// only do this if it doesn't exist yet
	if (\DBUtil::table_exists($table))
	{
	    return;
	}

        \DBUtil::create_table(
	    $table,
	    /* fields */
	    array(
		'uid' => array('type' => 'int'),
		'flag_id' => array('type' => 'int'),
		'gained_at' => array('type' => 'datetime'),
		'totalpoint' => array('type' => 'int'),
	    ),
	    /* primary_keys */
	    array('uid', 'flag_id'),
	    true, false, NULL,
	    /* foreign_keys */
	    array(
		array(
		    'key' => 'uid',
		    'reference' => array(
			'table' => 'users',
			'column' => 'id',
		    ),
		    'on_update' => 'CASCADE',
		    'on_delete' => 'CASCADE',
		),
/*
		array(
		    'key' => 'flag_id',
		    'reference' => array(
			'table' => 'flags',
			'column' => 'flag_id',
		    ),
		    'on_update' => 'CASCADE',
		    'on_delete' => 'CASCADE',
		),
*/
	    )
	);

	echo "Table created: ".$table."\n";
    }

    
    public function delete_gained_table()
    {
        $table = 'gained';
	\DBUtil::drop_table($table);
	echo "Table deleted: ".$table."\n";
    }


    public function create_times_table()
    {
        $table = 'times';
	
	// only do this if it doesn't exist yet
	if (\DBUtil::table_exists($table))
	{
	    return;
	}
        \DBUtil::create_table($table, array(
            'start_time' => array('type' => 'datetime'),
            'end_time' => array('type' => 'datetime'),
        ));

	echo "Table created: ".$table."\n";
    }


    public function delete_times_table()
    {
        $table = 'times';
	\DBUtil::drop_table($table);
	echo "Table deleted: ".$table."\n";
    }


    public function create_history_table()
    {
        $table = 'history';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
                'uid' => array('type' => 'int', 'constraint' => 11),
                'posted_at' => array('type' => 'datetime'),
		'result' => array('type' => 'varchar', 'constraint' => 10)
            ),
            /* primary_keys */
            array('uid', 'posted_at'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }


    public function delete_history_table()
    {
        $table = 'history';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function create_reviews_table()
    {
        $table = 'reviews';

        // only do this if it doesn't exist yet
        if (\DBUtil::table_exists($table))
        {
            return;
        }

        \DBUtil::create_table(
            $table,
            /* fields */
            array(
		'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
		'puzzle_id' => array('type' => 'int'),
		'score' => array('type' => 'int', 'constraint' => 4),
		'comment' => array('type' => 'varchar', 'constraint' => 255),
		'secret_comment' => array('type' => 'varchar', 'constraint' => 255),
                'uid' => array('type' => 'int', 'constraint' => 11),
		'updated_at' => array('type' => 'datetime'),
            ),
            /* primary_keys */
            array('id'),
            true, false, NULL,
            /* foreign_keys */
            array(
                array(
                    'key' => 'uid',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'CASCADE',
                ),
            )
        );

        echo "Table created: ".$table."\n";
    }

    
    public function delete_reviews_table()
    {
        $table = 'reviews';
        \DBUtil::drop_table($table);
        echo "Table deleted: ".$table."\n";
    }


    public function update_times($timelist = NULL)
    {
        if ($timelist == NULL){
            echo "Usage: php oil r ctfscore:update_times file\n";
            return;
        }
	require $timelist;
	$table = 'times';
	$start_time = $times['start_time'];
	$end_time = $times['end_time'];
	if (count(\DB::select()->from($table)->execute()) < 1)
	{
	    \DB::insert($table)->set(array(
		'start_time' => $start_time,
		'end_time' => $end_time
	    ))->execute();
            echo "Time inserted: START=".$start_time.": END=".$end_time."\n";
	}
	else
	{
	    \DB::update($table)->set(array(
		'start_time' => $start_time,
		'end_time' => $end_time
	    ))->execute();
            echo "Time updated: START=".$start_time.": END=".$end_time."\n";
	}
    }
    

    /* Insert flags from the php file specified by argument. */
    public function insert_flags($flaglist = NULL)
    {
        if ($flaglist == NULL){
            echo "Usage: php oil r ctfscore:insert_flags file\n";
            return;
        }
	require $flaglist;
	foreach ($flags as $flag_row) {
	    $flag_id = $flag_row['flag_id'];
	    $flag = $flag_row['flag'];
	    $point = $flag_row['point'];
	    $bonus_point = $flag_row['bonus_point'];
	    $this->insert_flag($flag_id, $flag, $point, $bonus_point);
	}
    }


    /* Insert the flag specified by arguments. */
    public function insert_flag($flag_id = NULL, $flag = NULL, $point = NULL, $bonus_point = NULL)
    {
        if (($flag_id == NULL) || ($flag == NULL) || ($point == NULL) || ($bonus_point == NULL)){
            echo "Usage: php oil r ctfscore:insert_flag flag_id flag point\n";
            return;
        }
	$table = 'flags';
	\DB::insert($table)->columns(array(
	    'flag_id', 'flag', 'point', 'bonus_point'
	))->values(array(
	    $flag_id, $flag, $point, $bonus_point
	))->execute();
        echo "Flag inserted: ".$flag_id.":".$flag.":".$point.":".$bonus_point."\n";
    }
    
    
    /* Insert users from the php file specified by argument. */
    public function insert_users($userlist = NULL)
    {
        if ($userlist == NULL){
            echo "Usage: php oil r ctfscore:insert_users file\n";
            return;
        }
        require $userlist;
        foreach ($users as $user){
            $username = $user["username"];
            $password = $user["password"];
            $this->insert_user($username, $password);
        }
    }


    /* Insert the user specified by arguments */
    public function insert_user($username = NULL, $password = NULL)
    {
        if (($username == NULL) || ($password == NULL)){
            echo "Usage: php oil r ctfscore:insert_user username password\n";
            return;
        }
        try {
            $auth = \Auth::instance();
            $dummyemail = rand() . '@dummy.com';
            if ($auth->create_user($username, $password, $dummyemail)) {
                echo "User inserted: " . $username . "\n";
                return;
            }
            $errmsg = "Failed to insert: " . $username . "\n";
        } catch (SimpleUserUpdateException $e) {
            $errmsg = $e->getMessage();
        }
        echo $errmsg;
    }


}
/* End of file tasks/ctfscore.php */
