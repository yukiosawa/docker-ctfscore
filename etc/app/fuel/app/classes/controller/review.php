<?php

class Controller_Review extends Controller_Template
{
    
    public function before()
    {
        parent::before();

        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

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


    public function get_editable_review($review_id)
    {
	list($driver, $userid) = Auth::get_user_id();
	$review = null;
	if ($reviews = Model_Review::get_reviews($review_id, null, null, true))
	{
	    $review = $reviews[0];
	}

	// 自分が投稿したレビュー
	if ($review && $userid == $review['uid'])
	{
	    return $review;
	}
	// 管理者の場合全てのレビュー
	elseif ($review && Controller_Auth::is_admin())
	{
	    return $review;
	}
	else
	{
	    return null;
	}
    }


    public function action_list($puzzle_id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($puzzle_id)) $puzzle_id = null;

	$data['my_name'] = Auth::get_screen_name();
	$data['reviews'] = Model_Review::get_reviews(null, $puzzle_id);
	$data['puzzle_id'] = $puzzle_id;
	$this->template->title = 'Reviews';
	$this->template->content = View::forge('review/list', $data);
	$this->template->footer = '';
    }


    public function action_view($id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($id)) $id = null;
	$data = '';
	$error_msg = '';
	if (!is_null($id) && count($reviews = Model_Review::get_reviews($id)) > 0)
	{
	    $data['review'] = $reviews[0];
	}
	else
	{
	    $error_msg = 'レビューIDの指定が無効です。';
	}
	$data['errmsg'] = $error_msg;
	$this->template->title = 'View';
	$this->template->content = View::forge('review/view', $data);
	$this->template->footer = View::forge('review/footer');
    }


    public function action_edit($id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($id)) $id = null;
	$error_msg = '';
	$data = '';
	
	if (!$review = $this->get_editable_review($id))
	{
	    $error_msg = '編集できません。';
	}
	else
	{
	    if (Input::method() == 'POST')
	    {
		// 入力パラメータチェック
		Controller_Auth::checkCSRF();
		$val = Model_Review::validate('edit');

		if ($val->run())
		{
		    $puzzle_id = $val->validated('puzzle_id');
		    $score = $val->validated('score');
		    $comment = $val->validated('comment');
		    $secret_comment = $val->validated('secret_comment');
//		    list($driver, $userid) = Auth::get_user_id();
		    // 作成時のユーザIDをそのまま引き継ぐ
		    $userid = $review['uid'];
		    $result = Model_Review::update_review($id, $puzzle_id, $score, $comment, $secret_comment, $userid);
		    if($result)
		    {
			// 管理画面へ通知

			// 成功画面へ転送
			$data['review'] = Model_Review::get_reviews($id)[0];
			$data['msg'] = '更新しました。';
			$this->template->title = 'Updated';
			$this->template->content = View::forge('review/created', $data);
			$this->template->footer = View::forge('review/footer');
			return;
		    }
		    else
		    {
			$error_msg = '更新に失敗しました。';
		    }
		}
		else
		{
		    $error_msg = $val->show_errors();
		}
	    }
	}

	$data['puzzles'] = Model_Score::get_puzzles();
	$data['review'] = $review;
	$this->template->title = 'レビュー投稿';
	$this->template->content = View::forge('review/edit', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('review/footer');
    }


    public function action_delete()
    {
        // POST以外は受け付けない
        Controller_Auth::checkAllowedMethod('POST');
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Review::validate('delete');

	$error_msg = '';
	$review = '';
	if ($val->run())
	{
	    $id = $val->validated('review_id');
	    if (!$review = $this->get_editable_review($id))
	    {
		$error_msg = '削除できません。';
	    }
	    else
	    {
		Model_Review::delete_review($id);
	    }
	}
	else
	{
	    $error_msg = $val->show_errors();
	}

	$data['review'] = $review;
	$data['errmsg'] = $error_msg;
	$data['msg'] = '削除しました。';
	$this->template->title = 'Delete';
	$this->template->content = View::forge('review/delete', $data);
	$this->template->footer = View::forge('review/footer');
    }

    
    public function action_create($puzzle_id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($puzzle_id)) $puzzle_id = null;
	$error_msg = '';
	if (Input::method() == 'POST')
	{
            // 入力パラメータチェック
            Controller_Auth::checkCSRF();
	    $val = Model_Review::validate('create');

	    if ($val->run())
	    {
		$id = '';
		$puzzle_id = $val->validated('puzzle_id');
		$score = $val->validated('score');
		$comment = $val->validated('comment');
		$secret_comment = $val->validated('secret_comment');
		list($driver, $userid) = Auth::get_user_id();
		$id = Model_Review::create_review($puzzle_id, $score, $comment, $secret_comment, $userid);
		if ($id)
		{
		    // 管理画面へ通知


		    $data['review'] = Model_Review::get_reviews($id)[0];
		    $data['msg'] = '作成しました。';
		    $this->template->title = 'Create';
		    $this->template->content = View::forge('review/created', $data);
		    $this->template->footer = View::forge('review/footer');
		    return;
		}
		else
		{
		    $error_msg = '作成に失敗しました。';
		}
	    }
	    else
	    {
		$error_msg = $val->show_errors();
	    }
	}

	$review['puzzle_id'] = $puzzle_id;
	$review['score'] = '';
	$review['comment'] = '';
	$review['secret_comment'] = '';

	$data['puzzles'] = Model_Score::get_puzzles();
	$data['review'] = $review;
	$this->template->title = 'レビュー投稿';
	$this->template->content = View::forge('review/create', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('review/footer');
    }
    
}
