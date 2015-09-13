<?php

class Controller_Download extends Controller
{

    public function before()
    {
        parent::before();

        // CTF開始前は許可しない
        Controller_Score::checkCTFStatus(true, false);
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();
    }


    // 問題の添付ファイルをダウンロードする
    public function get_puzzle()
    {
	// idとfile名をgetで受け付ける
	$puzzle_id = basename(Input::get('id'));
	$file_name = basename(Input::get('file'));

	// idにひもづく問題
	$dir = Model_Puzzle::get_attachment_dir($puzzle_id);
	$path = $dir.'/'.$file_name;
	if (file_exists($path))
	{
	    // ダウンロード
	    File::download($path);
	}

	// 見つかりませんページへ
	Response::redirect('auth/404');
    }


    // 問題回答後の画像ファイルをダウンロードする
    public function get_image()
    {
	// id、type(success/fail)、file名をgetで受け付ける
	$puzzle_id = basename(Input::get('id'));
	$type = basename(Input::get('type'));
	$file_name = basename(Input::get('file'));
	$path =  '';

	if ($type == 'success')
	{
	    // 回答済みの場合のみ画像を返す
	    list($driver, $userid) = Auth::get_user_id();
	    if (Model_Puzzle::is_answered_puzzle($userid, $puzzle_id))
	    {
		// 正解画像のパスをセット(問題により異なる)
		$dir = Model_Puzzle::get_success_image_dir($puzzle_id);
		$path = $dir.'/'.$file_name;
	    }
	}
	else if ($type == 'failure')
	{
	    // 不正解画像のパスをセット(全問題共通)
	    $dir = Model_Puzzle::get_failure_image_dir();
	    $path = $dir.'/'.$file_name;
	}

	if (file_exists($path))
	{
	    // ダウンロード
	    File::download($path);
	}
	else
	{
	    // 見つかりませんページへ
	    Response::redirect('auth/404');
	}
    }
}    
