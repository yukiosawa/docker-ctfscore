<?php
/* (1) DBに登録するテキストをこのファイルに記述する */
/* (2) 以下を実行する。
       php oil r ctfscore:insert_random_texts path_to_this_file */


$random_texts = array(
    // 正解時にランダムに表示するテキストメッセージ
    // (問題ごとに指定する場合はそちらが優先)
    'success' => array(
	'正解-1',
	'正解-2',
    ),
    // 失敗時にランダムに表示するテキストメッセージ
    // (問題ごとに指定する場合はそちらが優先)
    'failure' => array(
	'不正解-1',
	'不正解-2',
    ),
);

