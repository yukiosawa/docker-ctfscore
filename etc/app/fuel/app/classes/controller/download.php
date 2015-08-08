<?php

class Controller_Download extends Controller
{

    // 問題の添付ファイルをダウンロードする
    public function get_puzzle()
    {
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();

	// idとfile名をgetで受け付ける
	$flag_id = basename(Input::get('id'));
	$file_name = basename(Input::get('file'));

	// idにひもづく問題
	$puzzle = Score_Puzzle::get_puzzle($flag_id);
	if ($puzzle)
	{
	    $path = $puzzle['attachment_dir'].'/'.$file_name;
	    if (file_exists($path))
	    {
		// ダウンロード
		File::download($path);
	    }
	}

	// 見つかりませんページへ
	Response::redirect('auth/404');
    }


    // 問題回答後の画像ファイルをダウンロードする
    public function get_image()
    {
	// 認証済みユーザのみ許可
	Controller_Auth::redirectIfNotAuth();

	// id、type(success/fail)、file名をgetで受け付ける
	$flag_id = basename(Input::get('id'));
	$type = basename(Input::get('type'));
	$file_name = basename(Input::get('file'));
	$path =  '';

	if (($type == 'success') && Score_Puzzle::is_message_active('image', 'success'))
	{
	    // 回答済みの場合のみ画像を返す
	    list($driver, $userid) = Auth::get_user_id();
	    if (Model_Score::is_answered_flag($userid, $flag_id))
	    {
		// 正解画像のパスをセット(問題により異なる)
		$files = Score_Puzzle::get_success_files($flag_id);
		$path = $files['image_dir'].'/'.$file_name;
	    }
	}
	else if (($type == 'fail') && Score_Puzzle::is_message_active('image', 'fail'))
	{
	    // 不正解画像のパスをセット(全問題共通)
	    $files = Score_Puzzle::get_fail_files();
	    $path = $files['image_dir'].'/'.$file_name;
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
