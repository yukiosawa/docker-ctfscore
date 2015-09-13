<?php
    if (!empty($errmsg)) {
	echo "<div class='alert alert-danger'>$errmsg</div>";
    }
?>

<!-- メッセージ -->
<?php if ($result == 'success'): ?>
  <p class='alert alert-success h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == 'failure'): ?>
  <p class='alert alert-danger h4'><?php echo nl2br($text); ?></p>
<?php elseif ($result == 'duplicate'): ?>
  <p class='alert alert-info h4'><?php echo nl2br($text); ?></p>
<?php endif; ?>

<div class="row">
  <div class="col-md-5">
    <?php
    // 問題ごとのカスタム画像
    if (!empty($image_name))
    {
	echo "<p><image src='/download/image?id=".$puzzle_id.
		 "&type=".$result."&file=".$image_name.
		 "' class='img-responsive' /></p>\n";
    }
    ?>
  </div>
</div>


