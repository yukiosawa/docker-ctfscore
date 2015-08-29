<?php

return array(
    'scoreboard' => array(
	// trueの場合、回答済みのflagをスコアボードに表示する
	'show_my_answered' => false,
        // trueの場合、全員の回答内訳をスコアボードに表示する
        'show_all_answered_detail' => false,
    ),
    'puzzles' => array(
	// 問題ファイルを格納する場所
	'path_to_puzzles' => APPPATH.'ctfadmin/puzzles/',
	// カテゴリを記入するファイル名
	'category_file' => 'category.txt',
	// タイトルを記入するファイル名
	'title_file' => 'title.txt',
	// 本文を記入するファイル名
	'content_file' => 'content.txt',
	// 添付ファイルを格納するサブディレクトリ名
	'attachment_dir' => 'attachments',
	// 問題解答時に表示する画像
	'images' => array(
	    // trueの場合、正解時に画像表示する
	    'is_active_on_success' => true,
	    // 正解時に表示する画像ファイルを格納するサブディレクトリ名
	    'success_image_dir' => 'images_on_success',
	    // trueの場合、不正解時に画像表示する
	    'is_active_on_fail' => true,
	    // 不正解時に表示する画像ファイルを格納するディレクトリ
	    'fail_image_dir' => APPPATH.'ctfadmin/puzzles/images_on_fail',
	),
	// 問題解答時に表示するテキスト
	'texts' => array(
	    // trueの場合、正解時にテキスト表示する
	    'is_active_on_success' => true,
	    // 正解時に表示するテキストファイルを格納するサブディレクトリ名
	    'success_text_dir' => 'texts_on_success',
	    // trueの場合、不正解時にテキスト表示する
	    'is_active_on_fail' => true,
	    // 不正解時に表示するテキストファイルを格納するディレクトリ
	    'fail_text_dir' => APPPATH.'ctfadmin/puzzles/texts_on_fail',
	),
    ),
    'sound' => array(
	// trueの場合、問題正解時に音を鳴らす
	'is_active_on_success' => true,
	// 問題正解時に鳴らす音源ファイル[DOCROOTからの相対パス]
	'success_file' => '/audio/Doorbell-cheap-dingdong.ogg',
	// trueの場合、問題不正解時で音を鳴らす
	'is_active_on_fail' => true,
	// 問題不正解時にならす音源ファイル[DOCROOTからの相対パス]
	'fail_file' => '/audio/Buzzer.ogg',
	// trueの場合、ローカルサーバ上でサウンド再生[ローカル固有設定]
	'use_localhost' => false,
	// ローカルサーバ上で音源を再生するスクリプト[ローカル固有設定]
	'script' => APPPATH.'ctfadmin/sound/play.sh',
    ),
    'chart' => array(
	// グラフ描画の対象とする最大人数(下にあるcolorsの数以下とすること)
	'max_number_of_users' => 10,
	// グラフ描画の色
	'colors' => array(
	    'black',
	    'maroon',
	    'green',
	    'navy',
	    'gray',
	    'red',
	    'purple',
	    'olive',
	    'teal',
	    'yellow',
	    'coral',
	    'springgreen',
	    'orangered',
	    'lawngreen',
	    'pink',
	    'skyblue',
	    'brown',
	    'khaki',
	    'silver',
	    'lime',
	),
	// グラフをプロットする間隔(秒)
	'plot_interval_seconds' => 3600,
	// グラフをプロットする最大数
	'plot_max_steps' => 10000,
    ),
    'history' => array(
	// 試行回数を制限する間隔(秒)
	'attempt_interval_seconds' => 60,
	// 試行回数の制限値(回)
	'attempt_limit_times' => 5,
    ),
    'review' => array(
	// 最大評価点
	'max_data_number' => 10,
    ),
    'admin' => array(
	// 管理者ユーザのグループID
	'admin_group_id' => 100,
    ),
    'rule' => array(
	// 競技ルール
	'rule_file' => APPPATH.'ctfadmin/rule/rule.txt',
    ),
);

/* End of file ctfscore.php */
