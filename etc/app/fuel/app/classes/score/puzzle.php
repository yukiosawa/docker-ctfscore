<?php

class Score_Puzzle
{

    // 設定ファイルの各項目
    protected static $_prop = array(
	'path_to_puzzles',
	'category_file',
	'title_file',
	'content_file',
	'attachment_dir',
	'images' => array(
	    'is_active_on_success',
	    'success_image_dir',
	    'is_active_on_fail',
	    'fail_image_dir',
	),
	'texts' => array(
	    'is_active_on_success',
	    'success_text_dir',
	    'is_active_on_fail',
	    'fail_text_dir',
	),
    );


    public static function _init()
    {
	// 設定ファイル読み込み
	Config::load('ctfscore', true);
	self::$_prop['path_to_puzzles'] = Config::get(
	    'ctfscore.puzzles.path_to_puzzles');
	self::$_prop['category_file'] = Config::get(
	    'ctfscore.puzzles.category_file');
	self::$_prop['title_file'] = Config::get(
	    'ctfscore.puzzles.title_file');
	self::$_prop['content_file'] = Config::get(
	    'ctfscore.puzzles.content_file');
	self::$_prop['attachment_dir'] = Config::get(
	    'ctfscore.puzzles.attachment_dir');
	self::$_prop['images']['is_active_on_success'] = Config::get(
	    'ctfscore.puzzles.images.is_active_on_success');
	self::$_prop['images']['success_image_dir'] = Config::get(
	    'ctfscore.puzzles.images.success_image_dir');
	self::$_prop['images']['is_active_on_fail'] = Config::get(
	    'ctfscore.puzzles.images.is_active_on_fail');
	self::$_prop['images']['fail_image_dir'] = Config::get(
	    'ctfscore.puzzles.images.fail_image_dir');
	self::$_prop['texts']['is_active_on_success'] = Config::get(
	    'ctfscore.puzzles.texts.is_active_on_success');
	self::$_prop['texts']['success_text_dir'] = Config::get(
	    'ctfscore.puzzles.texts.success_text_dir');
	self::$_prop['texts']['is_active_on_fail'] = Config::get(
	    'ctfscore.puzzles.texts.is_active_on_fail');
	self::$_prop['texts']['fail_text_dir'] = Config::get(
	    'ctfscore.puzzles.texts.fail_text_dir');
    }


    // 問題解答時のメッセージ表示
    public static function is_message_active($type = null, $event = null)
    {
	if ($type == 'image')
	{
	    $type = 'images';
	}
	else if ($type == 'text')
	{
	    $type = 'texts';
	}
	if ($event == 'success')
	{
	    $event = 'is_active_on_success';
	}
	else if ($event == 'fail')
	{
	    $event = 'is_active_on_fail';
	}

	if (isset(self::$_prop[$type][$event]))
	{
	    return self::$_prop[$type][$event];
	}
	else
	{
	    return false;
	}
    }


    // 正解時に表示するファイル
    public static function get_success_files($puzzle_id = null)
    {
	$files = array();
	
	if ($puzzle_id == null)
	{
	    return $files;
	}

	$base_path = self::$_prop['path_to_puzzles'];
	// 画像
	$image_dir = self::$_prop['images']['success_image_dir'];
	$image_path = $base_path.$puzzle_id.'/'.$image_dir;
	// テキスト
	$text_dir = self::$_prop['texts']['success_text_dir'];
	$text_path = $base_path.$puzzle_id.'/'.$text_dir;
	try
	{
	    // path直下のファイルすべて
	    $files['images'] = File::read_dir($image_path, 1, array(
		'!.*' => 'dir', // ディレクトリは除く
	    ));
	    $files['image_dir'] = $image_path;
	    // path直下のファイルすべて
	    $files['texts'] = File::read_dir($text_path, 1, array(
		'!.*' => 'dir', // ディレクトリは除く
	    ));
	    $files['text_dir'] = $text_path;
	    return $files;
	}
	catch (InvalidPathException $e)
	{
	    return $files;
	}
    }


    // 不正解時に表示するファイル
    public static function get_fail_files()
    {
	$images = array();

	$image_path = self::$_prop['images']['fail_image_dir'];
	$text_path = self::$_prop['texts']['fail_text_dir'];
	try
	{
	    // path直下のファイルすべて
	    $files['images'] = File::read_dir($image_path, 1, array(
		'!.*' => 'dir', // ディレクトリは除く
	    ));
	    $files['image_dir'] = $image_path;
	    // path直下のファイルすべて
	    $files['texts'] = File::read_dir($text_path, 1, array(
		'!.*' => 'dir', // ディレクトリは除く
	    ));
	    $files['text_dir'] = $text_path;
	    return $files;
	}
	catch (InvalidPathException $e)
	{
	    return $files;
	}
    }


    // 問題を取得する
    public static function get_puzzle($puzzle_id = null)
    {
	$puzzle = array();
	
	if ($puzzle_id == null)
	{
	    return $puzzle;
	}

	// idとpoint
	$flag = Model_Score::get_flags($puzzle_id);
	if (!$flag)
	{
	    return $puzzle;
	}
	$puzzle['flag_id'] = $puzzle_id;
	$puzzle['point'] = $flag[0]['point'];

	$base_path = self::$_prop['path_to_puzzles'];
	$path = $base_path.$puzzle_id.'/';

	// 問題カテゴリ
	try
	{
	    $puzzle['category'] = File::read(
		$path.self::$_prop['category_file'], true);
	}
	catch (InvalidPathException $e)
	{
	    $puzzle['category'] = '指定なし';
	}
	// 問題タイトル
	try
	{
	    $puzzle['title'] = File::read(
		$path.self::$_prop['title_file'], true);
	}
	catch (InvalidPathException $e)
	{
	    $puzzle['title'] = '問題が用意されていません';
	}
	// 問題本文
	try
	{
	    $puzzle['content'] = File::read(
		$path.self::$_prop['content_file'], true);
	}
	catch (InvalidPathException $e)
	{
	    $puzzle['content'] = '';
	}
	// 添付ファイル
	$dir = $path.self::$_prop['attachment_dir'];
	$puzzle['attachment_dir'] = $dir;
	try
	{
	    // path直下のファイルすべて
	    $puzzle['attachments'] = File::read_dir($dir, 1, array(
		'!.*' => 'dir', // ディレクトリは除く
	    ));
	}
	catch (InvalidPathException $e)
	{
	    $puzzle['attachments'] = array();
	}

	return $puzzle;
    }
}

