<?php
if (!empty($errmsg)) {
  echo "<div class='alert alert-danger'>$errmsg</div>";
}
?>

<form class="form" action="/auth/removed" method="POST">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />
  <h4>
    <?php echo $username ?>を削除しますか？
  </h4>
  <button type="submit" class="btn btn-primary">削除する</button>
</form>


