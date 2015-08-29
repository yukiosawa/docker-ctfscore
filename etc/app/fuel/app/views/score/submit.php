<?php
    if (!empty($errmsg)) {
	echo "<div class='alert alert-danger'>$errmsg</div>";
    }
?>

<p>
  <?php
      echo $answer;
  ?>
</p>

<?php
    if ($result == 'success') {
	echo "<p class='alert alert-success h4'>正解！</p>";
	/* echo "<p>" . $point . "ポイント獲得。</p>"; */
    } elseif ($result == 'fail') {
	echo "<p class='alert alert-danger h4'>残念、不正解です。</p>";
    } elseif ($result == 'duplicate') {
	echo "<p class='alert alert-info h4'>既に回答済です。</p>";
    }
?>

<div class="row">
  <div class="col-md-5">
    <?php
        // 問題ごとのカスタムテキスト
	foreach ($texts as $text)
	{
	    $path = $text_dir.'/'.$text;
	    $content = File::read($path, true);
	    echo "<p>".nl2br($content)."</p>\n";
	}
        // 問題ごとのカスタム画像
	foreach ($images as $image)
	{
	    echo "<p><image src='/download/image?id=".$flag_id.
		 "&type=".$result."&file=".$image.
		 "' class='img-responsive' /></p>\n";
	}
    ?>
  </div>
</div>


