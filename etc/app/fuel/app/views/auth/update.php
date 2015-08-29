<?php
if (!empty($errmsg)) {
  echo "<div class='alert alert-danger'>$errmsg</div>";
}
?>

<form class="form-horizontal" action="/auth/updated" method="POST">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />
  <div class="form-group">
    <label class="col-md-2 control-label" for="oldpassword">旧パスワード</label>
    <div class="col-md-4">
      <input class="form-control" id="oldpassword" type="password" name="old_password" value="" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-md-2 control-label" for="newpassword">新パスワード</label>
    <div class="col-md-4">
      <input class="form-control" id="newpassword" type="password" name="password" value="" />
    </div>
  </div>



  <div class="form-group">
    <div class="col-md-offset-2 col-md-4">
      <button type="submit" class="btn btn-primary">ユーザー更新</button>
    </div>
  </div>
</form>


