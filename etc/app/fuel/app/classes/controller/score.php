<?php

class Controller_Score extends Controller_Template
{

    public function before()
    {
	parent::before();
    }


    // CTF実施状況に応じてステータスページへリダイレクトする
    // 第1引数true: 開始前の場合リダイレクト
    // 第2引数true: 終了後の場合リダイレクト
    public static function checkCTFStatus($before = true, $ended = true)
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

	// puzzle一覧
	$data['all_puzzles'] = Model_Puzzle::get_puzzles();

	// 全ユーザの回答状況一覧
	$data['scoreboard'] = Model_Score::get_scoreboard();

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
	// 管理者は許可しない
	if (Controller_Auth::is_admin()) {
	    Response::redirect('auth/invalid');
	}
	// POST以外は受け付けない
	Controller_Auth::checkAllowedMethod('POST');
	// 入力パラメータチェック
	Controller_Auth::checkCSRF();
	$val = Model_Score::validate('score_submit');

	$data = array();
	$answer = '';
	$result = '';
	$puzzle_id = '';
	$point = '';
	$error_msg = '';
	$image_dir = '';
	$image_name = '';
	$text = '';

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
	    $puzzle_id = Model_Puzzle::get_puzzle_id($answer);
	    if (!isset($puzzle_id))
	    {
		// 不正解
		$result = 'failure';

		// 管理画面へ通知
		$mgmt_msg = 'failed.';
		Model_Score::emitToMgmtConsole('fail', $mgmt_msg);
		// ローカル用サウンド
		Model_Score::sound('fail');

		// 表示するメッセージ(画像、テキスト)
		$msg = Model_Puzzle::get_failure_messages();
                // 取得できない場合はデフォルト値をセット
                $text = (!empty($msg['text'])) ? $msg['text'] : '不正解';
                $image_name = $msg['image_name'];
	    }
	    else
	    {
		// 回答済かどうかチェック
		if (Model_Puzzle::is_answered_puzzle($userid, $puzzle_id))
		{
		    // 既に正解済み
		    $result = 'duplicate';
		    $text = '既に回答済み';
		}
		else
		{
		    // 正解
		    $result = 'success';
		    $puzzle = Model_Puzzle::get_puzzles($puzzle_id)[0];
		    $point = $puzzle['point'];

		    // 獲得ポイントを更新
		    Model_Puzzle::set_puzzle_gained($userid, $puzzle_id);

		    // 管理画面へ通知
		    $mgmt_msg = $username.' solved the puzzle '.$puzzle_id.'.';
		    Model_Score::emitToMgmtConsole('success', $mgmt_msg);
		    // ローカル用サウンド
		    Model_Score::sound('success');

		    // 表示するメッセージ(画像、テキスト)
		    $msg = Model_Puzzle::get_success_messages($puzzle_id);
		    // 取得できない場合はデフォルト値をセット
                    $text = (!empty($msg['text'])) ? $msg['text'] : '正解';
                    $image_name = $msg['image_name'];
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
	$data['puzzle_id'] = $puzzle_id;
	$data['point'] = $point;
	$data['image_name'] = $image_name;
	$data['text'] = $text;
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
	$data['puzzles'] = Model_Puzzle::get_puzzles_addinfo();
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
	$data['rule_file'] = Config::get('ctfscore.rule.rule_file');
	$this->template->title = 'ルール';
	$this->template->content = View::forge('score/rule', $data);
	$this->template->footer = '';
    }

    
    public function action_profile($username)
    {
	// CTF開始前は許可しない
	$this->checkCTFStatus(true, false);
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();

	$data['profile'] = Model_Score::get_profile($username);
	$this->template->title = 'ユーザプロファイル';
	$this->template->content = View::forge('score/profile', $data);
	$this->template->footer = '';
    }
}
