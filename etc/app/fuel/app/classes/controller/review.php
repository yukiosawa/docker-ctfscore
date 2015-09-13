<?php

class Controller_Review extends Controller_Template
{
    
    public function before()
    {
        parent::before();

        // CTF開始前は許可しない
        Controller_Score::checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();
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


    public function action_edit($id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($id)) $id = null;
	$error_msg = '';
	$data = '';
	$puzzle_id = '';
	
	if (!$review = Model_Review::get_editable_review($id))
	{
	    $error_msg = '編集できません。';
	}
	else
	{
	    $puzzle_id = $review['puzzle_id'];
	    if (Input::method() == 'POST')
	    {
		// 入力パラメータチェック
		Controller_Auth::checkCSRF();
		$val = Model_Review::validate('edit');

		if ($val->run())
		{
		    // POSTのpuzzle_idは無視(review_idに紐づくpuzzle_id採用)
		    // $puzzle_id = $val->validated('puzzle_id');
		    $score = $val->validated('score');
		    $comment = $val->validated('comment');
		    $secret_comment = $val->validated('secret_comment');
		    // 作成時のユーザIDを引き継ぐ(更新ユーザは保持しない)
		    // list($driver, $userid) = Auth::get_user_id();
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

	$data['puzzles'] = Model_Puzzle::get_puzzles($puzzle_id);
	$data['review'] = $review;
	$this->template->title = 'レビュー投稿';
	$this->template->content = View::forge('review/edit', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('review/footer');
    }


    public function post_delete()
    {
        // 入力パラメータチェック
        Controller_Auth::checkCSRF();
        $val = Model_Review::validate('delete');

	$error_msg = '';
	$msg = '';
	$review = array();
	if ($val->run())
	{
	    $id = $val->validated('review_id');
            if (!$review = Model_Review::get_editable_review($id))
	    {
		$error_msg = '削除できません。';
	    }
	    else
	    {
		if (Model_Review::delete_review($id) < 1)
		{
		    $error_msg = '削除に失敗しました。';
		}
		else
		{
		    $msg = '削除しました。';
		}
	    }
	}
	else
	{
	    $error_msg = $val->show_errors();
	}

	$data['review'] = $review;
	$data['errmsg'] = $error_msg;
	$data['msg'] = $msg;
	$this->template->title = 'Delete';
	$this->template->content = View::forge('review/delete', $data);
	$this->template->footer = View::forge('review/footer');
    }

    
    public function action_create($puzzle_id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($puzzle_id)) $puzzle_id = null;
	$error_msg = '';
	$review['puzzle_id'] = '';
	$review['score'] = '';
	$review['comment'] = '';
	$review['secret_comment'] = '';
	$puzzles = '';

	list($driver, $userid) = Auth::get_user_id();
	if (Input::method() == 'POST')
	{
            // CSRFチェック
            Controller_Auth::checkCSRF();

	    $puzzle_id = Input::post('puzzle_id');
	    $score = Input::post('score');
	    $comment = Input::post('comment');
	    $secret_comment = Input::post('secret_comment');

            // 入力パラメータチェック
	    $val = Model_Review::validate('create');
	    if ($val->run())
	    {
		$id = '';
//		$puzzle_id = $val->validated('puzzle_id');
//		$score = $val->validated('score');
//		$comment = $val->validated('comment');
//		$secret_comment = $val->validated('secret_comment');
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

	    // エラー画面表示用に保持
	    $puzzles = Model_Puzzle::get_puzzles($puzzle_id);
	    $review['puzzle_id'] = $puzzle_id;
	    $review['score'] = $score;
	    $review['comment'] = $comment;
	    $review['secret_comment'] = $secret_comment;
	}
	else
	{
	    // GET
	    $puzzles = Model_Review::get_reviewable_puzzles($userid);
	    if (!$puzzles)
	    {
		$error_msg = 'レビュー投稿できる問題がありません。';
	    }
	    $review['puzzle_id'] = $puzzle_id;
	}

	$data['review'] = $review;
	$data['puzzles'] = $puzzles;
	$this->template->title = 'レビュー投稿';
	$this->template->content = View::forge('review/create', $data);
	$this->template->content->set_safe('errmsg', $error_msg);
	$this->template->footer = View::forge('review/footer');
    }
}
