<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::css('ctfscore.css'); ?>
    <?php echo Asset::js('jquery-2.1.1.min.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
  </head>

  <?php
  $logined = false;
  $my_name = '';
  $is_admin = false;
  $ctf_time = false;
  // ログイン状態の情報
  if (Auth::check())
  {
      $logined = true;
      $my_name = Auth::get_screen_name();
      $is_admin = Controller_Auth::is_admin();
  }
  else
  {
      $logined = false;
      $my_name = '';
      $is_admin = false;
  }
  // CTF時間の設定状況
  $status = Model_Score::get_ctf_time_status();
  if ($status['no_use'])
  {
      $ctf_time = false;
  }
  else
  {
      $ctf_time = true;
  }
  ?>
  
  <body>
    <div class="container">

      <!-- <nav class="navbar navbar-inverse"> -->
      <nav class="navbar">
	<ul class="nav navbar-nav">
	  <li><a href="/score/view">スコア</a></li>
	  <li><a href="/score/puzzle">問題</a></li>
	  <li><a href="/review/list">レビュー</a></li>
	  <?php if ($ctf_time): ?>
	    <li><a href="/score/chart">グラフ</a></li>
	    <li><a href="/score/status">実施状況</a></li>
	  <?php endif; ?>
	  <li><a href="/score/rule">ルール</a></li>
	</ul>
	<ul class="nav navbar-nav navbar-right">
	  <?php if ($is_admin): ?>
	    <li class="dropdown update">
	      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		管理者
	      </a>
	      <ul class="dropdown-menu">
		<li><a href="/admin/review/list">レビュー(管理者モード)</a></li>
		<li><a href="/mgmt/" target="_blank">管理コンソール</a></li>
	      </ul>
	    </li>
	  <?php endif; ?>
	  <?php if ($logined): ?>
	    <li class="dropdown update">
	      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<?php echo $my_name ?>としてログイン中
	      </a>
	      <ul class="dropdown-menu">
		<li><a href="/auth/update">パスワード変更</a></li>
		<li><a href="/auth/remove">ユーザ情報削除</a></li>
		<li><a href="/auth/logout">ログアウト</a></li>
	      </ul>
	    </li>
	  <?php else: ?>
	    <li><a href="/auth/login">ログインする</a></li>
	  <?php endif; ?>
	</ul>
      </nav>

      <?php echo $content; ?>
      <?php echo $footer; ?>
    </div>
  </body>
</html>
