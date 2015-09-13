<?php

class Model_Score extends Model
{

    // その問題の最初の正解者かどうか
    public static function is_first_winner($puzzle_id = null)
    {
        $result = DB::select()->from('gained')
			      ->where('puzzle_id', $puzzle_id)
			      ->execute()->as_array();
	if (count($result) > 0)
	{
	    return false;
	}
	else
	{
	    return true;
	}
    }

    // スコアボード全体を返す
    public static function get_scoreboard()
    {
	// 全員のスコア一覧 (管理者ユーザは表示しない)
	$admin_group_id = Config::get('ctfscore.admin.admin_group_id');
	$scores = DB::select('id', 'username', 'totalpoint', 'pointupdated_at')
	                ->from('users')->where('group', '!=', $admin_group_id)
			->order_by('totalpoint', 'desc')
	                ->order_by('pointupdated_at', 'asc')
	                ->execute()->as_array();
	return $scores;
    }

    
    // 管理画面へイベントを通知
    public static function emitToMgmtConsole($event = NULL, $msg = NULL)
    {
	require('/app/nodejs/socket.io-php-emitter/vendor/autoload.php');
	require('/app/nodejs/socket.io-php-emitter/src/Emitter.php');
	// Below initialization will create a  phpredis client, or a TinyRedisClient depending on what is installed
	$emitter = new SocketIO\Emitter(array('port' => '6379', 'host' => '127.0.0.1'));

	// broadcast can be replaced by any of the other flags
	/* $emitter->broadcast->emit('php', 'TEST from PHP'); */
	$emitter->emit($event, $msg);
    }


    // サーバローカル上で音を鳴らす
    public static function sound($type = NULL)
    {
	Config::load('ctfscore', true);
	if (!Config::get('ctfscore.sound.use_localhost'))
	{
	    return;
	}
	$script = Config::get('ctfscore.sound.script');
	if ($type == 'success')
	{
	    // 設定ファイルがOFFのときはなにもしない
	    if (!Config::get('ctfscore.sound.is_active_on_success'))
	    {
		return;
	    }
	    $file = DOCROOT.Config::get('ctfscore.sound.success_file');
	}
	else if ($type == 'fail')
	{
	    // 設定ファイルがOFFのときはなにもしない
	    if (!Config::get('ctfscore.sound.is_active_on_fail'))
	    {
		return;
	    }
	    $file = DOCROOT.Config::get('ctfscore.sound.fail_file');
	}
	else
	{
	    return;
	}

	if (file_exists($script) && file_exists($file))
	{
	    // 再生用スクリプトを実行
	    $cmd = $script.' '.$file;
	    Model_Score::exec_async($cmd);
	}
    }


    // 非同期でOSコマンドを実行する
    public static function exec_async($cmd = NULL)
    {
	if ($cmd == NULL)
	{
	    return;
	}

	if (PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT')
	{
	    exec($cmd . ' > /dev/null 2>&1 &');
	}
	else
	{
	    $fp = popen('start ' . $cmd, 'r');
	    pclose($fp);
	}
    }


    // グラフ描画用データを返す
    public static function get_ranking_chart()
    {
	// 横軸の項目
	// 開始と終了時刻
	$times = DB::select()->from('times')->execute()->as_array();
	if (count($times) < 1)
	{
	    return;
	}
	$start_time = $times[0]['start_time'];
	$end_time = $times[0]['end_time'];
	// プロット間隔(秒)
	$interval_seconds = Config::get('ctfscore.chart.plot_interval_seconds');
	// 最大プロット数
	$max_steps = Config::get('ctfscore.chart.plot_max_steps');

	$labels = array();
	$now = Model_Score::get_current_time();
	$label = $start_time;
	// 開始時刻からプロット間隔で時刻を取得して横軸とする
	for ($i = 0; $i < $max_steps; $i++)
	{
	    $labels[] = $label;
	    $added_time = Model_Score::get_mod_time($label, 'add', $interval_seconds);
	    $label = $added_time;

	    //現在時刻 or 終了時刻まで
	    $end = '';
	    if (strtotime($now) < strtotime($end_time))
	    {
		$end = $now;
	    }
	    else
	    {
		$end = $end_time;
	    }
	    if (strtotime($label) > strtotime($end))
	    {
		$labels[] = $end;
		break;
	    }
	}
	$result['labels'] = $labels;
	
	// 上位のユーザだけ対象とする。また0点は対象外とする。
	// 管理者も対象外
	$max_number = Config::get('ctfscore.chart.max_number_of_users');
        $admin_group_id = Config::get('ctfscore.admin.admin_group_id');
        $users = DB::select('id', 'username')
                     ->from('users')
		     ->where('totalpoint', '>', 0)
		     ->where('group', '!=', $admin_group_id)
		     ->order_by('totalpoint', 'desc')
		     ->limit($max_number)
		     ->order_by('pointupdated_at', 'asc')
		     ->execute()->as_array('id');
	if (count($users) < 1)
	{
	    return;
	}

	// ユーザ名一覧とグラフの色
	$userlist = array();
	$colors = Config::get('ctfscore.chart.colors');
	$cnt = 0;
	foreach ($users as $user)
	{
	    // 上位ユーザから順に色を割り当て
	    if (count($colors) < $cnt + 1)
	    {
		break;
	    }
	    $userlist += array($user['username'] => $colors[$cnt]);
	    $cnt++;
	}

	// 各ユーザの獲得済み総スコア履歴
	$gained = DB::select('username', 'gained_at', 'gained.totalpoint')
		     ->from('gained')
		     ->where('uid', 'IN', array_keys($users))
		     ->join('users', 'LEFT')
		     ->on('gained.uid', '=', 'users.id')
		     ->execute()->as_array();

	$result['userlist'] = $userlist;
	$result['pointlist'] = $gained;
	return $result;
    }


    public static function get_profile_chart($username = NULL)
    {
	$userid = '';
	if (!$username)
	{
	    // 指定されない場合はログイン中のユーザIDとする
	    list($driver, $userid) = Auth::get_user_id();
	    $username = Auth::get_screen_name();
	}
	else
	{
	    $userid = Model_Score::get_uid($username);
	}
	if (!$userid) return;

	$puzzles = Model_Puzzle::get_puzzles_addinfo($userid);

	// カテゴリごとに獲得スコア／総スコアを算出
	$categories = array();
	foreach ($puzzles as $puzzle)
	{
	    $category = $puzzle['category'];
	    $point = $puzzle['point'];
	    if (!array_key_exists($category, $categories))
	    {
		$categories[$category]['totalpoint'] = 0;
		$categories[$category]['point'] = 0;
	    }
	    $categories[$category]['totalpoint'] += $point;
	    if ($puzzle['answered'])
	    {
		$categories[$category]['point'] += $point;
	    }
	}
	$result['username'] = $username;
	$result['categories'] = $categories;

	return $result;
    }

    
    // 個人プロファイルを返す
    public static function get_profile($username = NULL)
    {
	$userid = '';
	if (!$username)
	{
	    // 指定されない場合はログイン中のユーザIDとする
	    list($driver, $userid) = Auth::get_user_id();
	    $username = Auth::get_screen_name();
	}
	else
	{
	    $userid = Model_Score::get_uid($username);
	}
	if (!$userid) return;


	$answered = Model_Puzzle::get_answered_puzzles($userid);
	$result['username'] = $username;
	$result['answered_puzzles'] = $answered;
	$result['reviews'] = Model_Review::get_reviews(null, null, $userid);
	return $result;
    }


    // usernameをuseridに変換する
    public static function get_uid($username = NULL)
    {
	if (!$username) return;
	
	$result = DB::select('id')->from('users')
				  ->where('username', $username)
				  ->execute()->as_array();
	if (count($result) > 0)
	{
	    return $result[0]['id'];
	}
	else
	{
	    return;
	}
    }
    
    
    // 現在のCTF実施状況を返す(開始前、実施中、終了)
    public static function get_ctf_time_status()
    {
	$status = array(
	    'start_time' => '',
	    'end_time' => '',
	    'before' => false,
	    'ended' => false,
	    'running' => false,
	    'no_use' => false,
	);
	
	$times = DB::select()->from('times')->execute()->as_array();
	// CTF時間設定なしの場合は常時実施中とする
	if (count($times) < 1)
	{
	    $status['no_use'] = true;
	    $status['start_time'] = 'N/A';
	    $status['end_time'] = 'N/A';
	    return $status;
	}

	// 開始時刻
	$status['start_time'] = $times[0]['start_time'];
	$start_unix_time = strtotime($status['start_time']);
	// 終了時刻
	$status['end_time'] = $times[0]['end_time'];
	$end_unix_time = strtotime($status['end_time']);
	// 現在時刻
	$now_unix = strtotime(Model_Score::get_current_time());

	if ($now_unix < $start_unix_time)
	{
	    $status['before'] = true;
	}
	else if ($now_unix < $end_unix_time)
	{
	    $status['running'] = true;
	}
	else
	{
	    $status['ended'] = true;
	}
	return $status;
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

	if (($factory == 'login') || ($factory == 'create'))
	{
	    $val->add('username', 'ユーザー名')
		->add_rule('required')
		->add_rule('max_length', 15)
		->add_rule('valid_string',
			   array(
			       'alpha',
			       'numeric',
			       'punctuation',
			       'dashes',
			       'quotes',
			       'brackets',
			       'braces',
			       'utf8'
			   ));
	    $val->add('password', 'パスワード')
		->add_rule('required')
		->add_rule('min_length', 4)
		->add_rule('max_length', 20);
	}
	else if ($factory == 'update')
	{
	    $val->add('password', '新パスワード')
	        ->add_rule('required')
		->add_rule('min_length', 6)
		->add_rule('max_length', 20);
	    $val->add('old_password', '旧パスワード')
	        ->add_rule('required')
		->add_rule('min_length', 6)
		->add_rule('max_length', 20);
	}
	else if ($factory == 'score_submit')
	{
	    $val->add('answer', 'flag')
	        ->add_rule('required')
		->add_rule('max_length', 255);
	}

	return $val;
    }

    
    // 回答試行数制限を超過しているかどうかを返す
    public static function is_over_attempt_limit($uid = NULL)
    {
	$interval_seconds = Config::get('ctfscore.history.attempt_interval_seconds');
	$limit_times = Config::get('ctfscore.history.attempt_limit_times');
	$now = Model_Score::get_current_time();
	$subed_time = Model_Score::get_mod_time($now, 'sub', $interval_seconds);

	$query = DB::select()->from('history');
	$query->where('uid', '=', $uid);
	$query->where('posted_at', '>', $subed_time);
	// 正解時のポストは除外しておく
	$query->where('result', '!=', 'success');
	$result = $query->execute();

	if (count($result) >= $limit_times)
	{
	    return true;
	}
	else
	{
	    return false;
	}
    }


    // 試行履歴を記録する
    public static function set_attempt_history($uid = NULL, $type = NULL)
    {
	// DB更新
	$now = Model_Score::get_current_time();
	try
	{
	    DB::start_transaction();
	    DB::insert('history')->set(array(
		'uid' => $uid,
		'posted_at' => $now,
		'result' => $type
		))->execute();
	    DB::commit_transaction();
	}
	catch (Exception $e)
	{
          /* ロールバック */
          DB::rollback_transaction();
          throw $e;
	}
    }


    // 現在時刻を返す
    public static function get_current_time()
    {
	// DBの時刻を基準とする
	// MySQL DATETIME型 "YYYY-MM-DD hh:mm:ss"
	$query = DB::select(DB::expr("NOW()"));
	$result = $query->execute()->as_array();
	list($key, $val) = each($result[0]);
	return $val;
    }


    // 時刻を加減算して返す
    public static function get_mod_time($time = NULL, $type = NULL, $interval_seconds = 0)
    {
	if ($time == NULL)
	{
	    return NULL;
	}

	// DBの時刻を基準とする
	// MySQL DATETIME型 "YYYY-MM-DD hh:mm:ss"
	if ($type == 'add')
	{
	    // インターバル秒数を加算
	    $query = DB::select(DB::expr(
		"'".$time."' + INTERVAL ".$interval_seconds." SECOND"));
	}
	elseif ($type == 'sub')
	{
	    // インターバル秒数を減算
	    $query = DB::select(DB::expr(
		"'".$time."' - INTERVAL ".$interval_seconds." SECOND"));
	}
	else
	{
	    return NULL;
	}

	$result = $query->execute()->as_array();
	list($key, $val) = each($result[0]);
	return $val;
    }
}
