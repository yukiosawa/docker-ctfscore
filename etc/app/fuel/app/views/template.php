<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <?php echo Asset::css('bootstrap.css'); ?>
    <?php echo Asset::js('jquery-2.1.1.min.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
    <style>
     body { margin: 20px; }
    </style>
  </head>
  <body>
    <div class="container">

      <nav class="navbar navbar-inverse">
	<ul class="nav navbar-nav">
	  <li><a href="/score/view">スコア</a></li>
	  <li><a href="/score/puzzle">問題</a></li>
	  <?php if ($ctf_time): ?>
	    <li><a href="/score/chart">グラフ</a></li>
	    <li><a href="/score/status">実施状況</a></li>
	  <?php endif; ?>
	</ul>
	<ul class="nav navbar-nav navbar-right">
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
    </div>
  </body>
</html>
