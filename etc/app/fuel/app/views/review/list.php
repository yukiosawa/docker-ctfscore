<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>

<?php $is_admin_url = Controller_Auth::is_admin_url(); ?>

<p>
  <!-- <div class="row">
   <div class="col-md-2">
   レビュー一覧
   <?php if(isset($puzzle_id)) echo '[問題'.$puzzle_id.']'; ?>
   </div>
   <div class="col-md-2"> -->

  <?php
  $path = '/review/create/';
  if ($is_admin_url) $path = '/admin' . $path;
  if(isset($puzzle_id)) $path = $path . $puzzle_id;
  ?>
  <a href="<?php echo $path; ?>" class="btn btn-primary">レビューする</a>

<!-- </div>
     </div> -->
</p>


<div class="row">
<table class="table table-hover">
  <thead>
    <tr>
      <?php if($is_admin_url): ?>
      <th class="col-md-2">問題タイトル</th><th class="col-md-2">評価</th><th class="col-md-3">公開コメント</th><th class="col-md-3">管理者へのメッセージ</th><th class="col-md-1">評価者</th><th class="col-md-1">更新日時</th><th class="col-md-1"></th>
      <?php else: ?>
      <th class="col-md-2">問題タイトル</th><th class="col-md-2">評価</th><th class="col-md-5">公開コメント</th><th class="col-md-1">評価者</th><th class="col-md-1">更新日時</th><th class="col-md-1"></th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($reviews as $item): ?>
    <tr>
      <td><?php echo $item['puzzle_id'].': '.$item['puzzle_title']; ?></td>
      <td><div class="review" data-number="<?php echo \Config::get('ctfscore.review.max_data_number');?>" data-score="<?php echo $item['score']; ?>"><div></td>
      <td><?php echo nl2br($item['comment']); ?></td>
      <?php if($is_admin_url): ?>
	<td><?php echo nl2br($item['secret_comment']); ?></td>
      <?php endif; ?>
      <td><?php echo $item['username']; ?></td>
      <td><?php echo $item['updated_at']; ?></td>
      <td>
	<?php if($my_name == $item['username'] || $is_admin_url): ?>
	  <?php
	  $edit_path = '/review/edit/' . $item['id'];
	  $del_path = '/review/delete/' . $item['id'];
	  if ($is_admin_url){
	      $edit_path = '/admin' . $edit_path;
	      $del_path = '/admin' . $del_path;
	  }
	  ?>
	  <a href="<?php echo $edit_path; ?>" class="btn btn-primary">編集</a>
	  <a href="<?php echo $del_path; ?>" class="btn btn-primary" onclick="return confirm('削除しますか？')">削除</a>
	<?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

</div>



