<?php

class Controller_Score extends Controller_Template
{

    public function before()
    {
	parent::before();

	/* // ログイン状態の情報
	   if (Auth::check())
	   {
	   $this->template->logined = true;
	   $this->template->my_name = Auth::get_screen_name();
	   $this->template->is_admin = Controller_Auth::is_admin();
	   }
	   else
	   {
	   $this->template->logined = false;
	   $this->template->my_name = '';
	   $this->template->is_admin = false;
	   }
	   // CTF時間の設定状況
	   $status = Model_Score::get_ctf_time_status();
	   if ($status['no_use'])
	   {
	   $this->template->ctf_time = false;
	   }
	   else
	   {
	   $this->template->ctf_time = true;
	   } */
    }


    // CTF実施状況に応じてステータスページへリダイレクトする
    // 第1引数true: 開始前の場合リダイレクト
    // 第2引数true: 終了後の場合リダイレクト
    public function checkCTFStatus($before = true, $ended = true)
    {
	$status = Model_Score::get_ctf_time_status();
	// 開始前のリダイレクト ($before=true指定)
	if ($before && $status['before'])
	{
	    Response::redirect('score/status');
	}
	// 終了後のリダイレクト ($end=true指定)
	if ($ended && $status['ended'])
	{
	    Response::redirect('score/status');
	}
    }


    // CTFの実施状況
    public function action_status()
    {
	$status = Model_Score::get_ctf_time_status();
	if ($status['before'])
	{
	    $data['status'] = '開始前です';
	}
	else if ($status['running'])
	{
	    $data['status'] = '実施中です';
	}
	else if ($status['ended'])
	{
	    $data['status'] = '終了しました';
	}
	else if ($status['no_use'])
	{
	    $data['status'] = '実施中です';
	}
	else
	{
	    $data['status'] = '不明';
	}
	$data['start_time'] = $status['start_time'];
	$data['end_time'] = $status['end_time'];
	$this->template->title = "実施状況";
	$this->template->content = View::forge('score/status', $data);
	$this->template->footer = View::forge('score/footer');
    }
    

    // スコアボード
    public function action_view()
    {
	// 認証済みユーザのみ許可
	// Controller_Auth::redirectIfNotAuth();

	// 自分のユーザ名
	$data['my_name'] = Auth::get_screen_name();

	// 自分が回答済みの答え一覧
	$show_my_answered = Config::get('ctfscore.scoreboard.show_my_answered');
	if ($show_my_answered)
	{
	    list($driver, $userid) = Auth::get_user_id();
	    $data['my_answered'] = Model_Score::get_answered_flags($userid);
	}
	$data['show_my_answered'] = $show_my_answered;

	// flag一覧
	$data['all_flags'] = Model_Score::get_flags();

	// 全ユーザの回答状況一覧
	$data['scoreboard'] = Model_Score::get_scoreboard();
	$data['show_all_answered_detail'] = Config::get('ctfscore.scoreboard.show_all_answered_detail');

	$this->template->title = "スコアボード";
	$this->template->content = View::forge('score/view', $data);
	$this->template->footer = '';
    }


    public function action_submit()
    {
	// CTF開始前と終了後は許可しない
	$this->checkCTFStatus(true, true);
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();
	// POST以外は受け付けない
	Controller_Auth::checkAllowedMethod('POST');
	// 入力パラメータチェック
	Controller_Auth::checkCSRF();
	$val = Model_Score::validate('score_submit');

	$data = array();
	$answer = '';
	$result = '';
	$flag_id = '';
	$point = '';
	$error_msg = '';
	$images = array();
	$texts = array();
	$text_dir = '';

	// ユーザID
	list($driver, $userid) = Auth::get_user_id();
	$username = Auth::get_screen_name();

	// 回数制限のチェック
	if (Model_Score::is_over_attempt_limit($userid))
	{
	    $result = 'error';
	    $error_msg = '連続回数制限に達しました。時間を空けてやり直してください。';
	}
	elseif ($val->run())
	{
	    // POSTされた回答が正解かチェック
	    $answer = $val->validated('answer');
	    $flags = Model_Score::get_flags(NULL, $answer, NULL);
	    if (count($flags) != 1)
	    {
		// 不正解
		$result = 'fail';

		// 管理画面へ通知
		$msg = 'failed.';
		Model_Score::emitToMgmtConsole('fail', $msg);
		// ローカル用サウンド
		Model_Score::sound('fail');

		// 表示するメッセージ
		$files = Score_Puzzle::get_fail_files();
		if (Score_Puzzle::is_message_active('image', 'fail') && isset($files['images']))
		{
		    // ファイル名のみ
		    //$images = $files['images'];
		    // 失敗時は画像をランダムに選ぶ
		    if (count($files['images']) > 0)
		    {
			$cnt = rand() % count($files['images']);
			$images[0] = $files['images'][$cnt];
		    }
		}
		if (Score_Puzzle::is_message_active('text', 'fail') && isset($files['texts']))
		{
		    // ファイルのフルパスを保持してviewへ渡す
		    $texts = $files['texts'];
		    $text_dir = $files['text_dir'];
		}
	    }
	    else
	    {
		// 回答済かどうかチェック
		$flag = $flags[0];
		if (Model_Score::is_answered_flag(
		    $userid, $flag['flag_id']))
		{
		    // 既に正解済み
		    $result = 'duplicate';
		}
		else
		{
		    // 正解
		    $result = 'success';
		    $flag_id = $flag['flag_id'];
		    $point = $flag['point'];

		    // 獲得ポイントを更新
		    Model_Score::set_flag_gained($userid, $flag['flag_id']);

		    // 管理画面へ通知
		    $msg = $username.' solved the puzzle '.$flag['flag_id'].'.';
		    Model_Score::emitToMgmtConsole('success', $msg);
		    // ローカル用サウンド
		    Model_Score::sound('success');

		    // 表示するメッセージ
		    $files = Score_Puzzle::get_success_files($flag['flag_id']);
		    if (Score_Puzzle::is_message_active('image', 'success') && isset($files['images']))
		    {
			// ファイル名のみ
			$images = $files['images'];
		    }
		    if (Score_Puzzle::is_message_active('text', 'success') && isset($files['texts']))
		    {
			// ファイルのフルパスを保持してviewへ渡す
			$texts = $files['texts'];
			$text_dir = $files['text_dir'];
		    }
		}
	    }
	}
        else
        {
	    $result = 'error';
	    $error_msg = $val->show_errors();
	}

	// 試行履歴を記録する
        Model_Score::set_attempt_history($userid, $result);

	$data['answer'] = $answer;
	$data['result'] = $result;
	$data['flag_id'] = $flag_id;
	$data['point'] = $point;
	$data['images'] = $images;
	$data['texts'] = $texts;
	$data['text_dir'] = $text_dir;
	$this->template->title = '回答結果';
	$this->template->content = View::forge('score/submit', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('score/footer');
    }


    public function action_puzzle()
    {
	// CTF開始前は許可しない
	$this->checkCTFStatus(true, false);
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();

	// 問題一覧
	$data['puzzles'] = Model_Score::get_puzzles();

	$this->template->title = '問題一覧';
	$this->template->content = View::forge('score/puzzle', $data);
	$this->template->footer = '';
    }


    public function action_chart()
    {
	$this->template->title = 'スコアグラフ';
	$status = Model_Score::get_ctf_time_status();
	if ($status['no_use'])
	{
	    // CTF時間設定なしの場合はグラフ描画しない
	    $this->template->content = 'N/A';
	    $this->template->footer = '';
	}
	else
	{
	    $this->template->content = View::forge('score/chart');
	    $this->template->footer = '';
	}
    }


    public function action_rule()
    {
	$data['rule'] = File::read(
	    Config::get('ctfscore.rule.rule_file'), true);
	$this->template->title = 'ルール';
	$this->template->content = View::forge('score/rule', $data);
	$this->template->footer = '';
    }
}
