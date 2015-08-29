<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>

<div class="row">
  <table class="table">
    <thead>
      <tr>
	<th>問題タイトル</th><th>評価</th><th>公開コメント</th><th>管理者へのメッセージ</th><th>評価者</th><th>更新日時</th>
      </tr>
    </thead>

    <tbody>
      <tr>
	<td><?php echo $review['puzzle_id'].': '.$review['puzzle_title']; ?></td>
	<td><div class="review" data-number="<?php echo \Config::get('ctfscore.review.max_data_number');?>" data-score="<?php echo $review['score']; ?>"></div></td>
	<td><?php echo nl2br($review['comment']); ?></td>
	<td><?php echo nl2br($review['secret_comment']); ?></td>
	<td><?php echo $review['username']; ?></td>
	<td><?php echo $review['updated_at']; ?></td>
      </tr>
    </tbody>

  </table>
</div>

