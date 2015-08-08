<?php
if (!empty($errmsg)) {
  echo "<div class='alert alert-danger'>$errmsg</div>";
}
?>
  
<form class="form-horizontal" action="/auth/login" method="POST">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />
  <div class="form-group">
    <label class="col-md-2 control-label" for="username">ユーザ名</label>
    <div class="col-md-4">
      <input class="form-control" id="username" type-"text" name="username" value="" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label" for="password">パスワード</label>
    <div class="col-md-4">
      <input class="form-control" id="password" type="password" name="password" value="" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-4">
      <button type="submit" class="btn btn-primary">ログイン</button>
    </div>
  </div>
</form>

<div class="row">
  <div class="col-md-offset-2 col-md-4 text-right">
    <a href="/auth/create">新規ユーザー作成</a>
  </div>
</div>

