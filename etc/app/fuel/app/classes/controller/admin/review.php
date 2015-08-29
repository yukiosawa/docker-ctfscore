<?php

class Controller_Admin_Review extends Controller_Review
{
    /* public $template = 'admin/template'; */
    
    public function before()
    {
	parent::before();

        // 管理者グループのみ許可
	if (!Controller_Auth::is_admin())
        {
	    Response::redirect('auth/invalid');
        }
    }


    public function action_list($puzzle_id = null)
    {
	// 入力された文字列が数字のみで構成されるかチェック。
	if (!ctype_digit($puzzle_id)) $puzzle_id = null;

	$data['my_name'] = Auth::get_screen_name();
	// 管理者へのメッセージも取得(第4引数 admin = true)
	$data['reviews'] = Model_Review::get_reviews(null, $puzzle_id, null, true);
	$data['puzzle_id'] = $puzzle_id;
	$this->template->title = 'Reviews';
	$this->template->content = View::forge('review/list', $data);
	$this->template->footer = '';
    }
}



