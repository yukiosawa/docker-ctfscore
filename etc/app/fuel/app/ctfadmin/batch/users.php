<?php

/* DBに登録したいユーザをこのファイルに記述する */
/* パスワードは4文字以上とすること */
/* (そうしないとログイン時のチェックでエラーとなってしまう) */

$users = array(
    array(
        'username' => 'user0',
        'password' => 'password0',
	'admin' => true,
    ),
    array(
        'username' => 'user1',
        'password' => 'password1',
	'admin' => false,
    ),
    array(
        'username' => 'user2',
        'password' => 'password2',
	'admin' => false,
    ),
    array(
        'username' => 'user3',
        'password' => 'password3',
	'admin' => false,
    ),
    array(
        'username' => 'user4',
        'password' => 'password4',
	'admin' => false,
    ),
    array(
        'username' => 'user5',
        'password' => 'password5',
	'admin' => false,
    ),
)
?>

