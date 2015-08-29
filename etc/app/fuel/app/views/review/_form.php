<?php echo Asset::js('jquery.raty.js'); ?>
<?php echo Asset::js('ctfscore-raty.js'); ?>

<form action="<?php echo $action ?>" method="post">
  <!-- CSRF対策 -->
  <input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />

  <div class="form-group">
    <label for="puzzle_id_title">問題</label>
    <?php $puzzle_id = (isset($review)) ? $review['puzzle_id'] : ''; ?>
    <select class="form-control" id="puzzle_id" name="puzzle_id">
      <?php foreach ($puzzles as $puzzle): ?>
	<!-- わかりやすくするためtitleを付加(送信時に除去) -->
	<option <?php if($puzzle['flag_id'] == $puzzle_id) echo " selected" ?>><?php echo $puzzle['flag_id'].': '.$puzzle['title'] ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  
  <div class="form-group">
    <div><label>評価</label></div>
    <?php $score = (isset($review)) ? $review['score'] : ''; ?>
    <div class="review-edit" data-number="<?php echo \Config::get('ctfscore.review.max_data_number');?>" data-score="<?php echo $score; ?>"></div>
    <input type="hidden" class="form-control" id="review-score" name="score" value="<?php echo $score; ?>"></input>
  </div>

  <div class="form-group">
    <label for="comment">公開コメント【全ユーザに公開：フラグや解き方のヒントは厳禁】</label>
    <textarea class="form-control" id="comment" name="comment" rows="5" placeholder="全員に公開されます。フラグや解き方のヒントは厳禁です。"><?php if(isset($review)) echo $review['comment']; ?></textarea>
  </div>
  <div class="form-group">
    <label for="secret_comment">管理者へのメッセージ【非公開】</label>
    <textarea class="form-control" id="secret_comment" name="secret_comment" rows="5" placeholder="他の参加者には公開されません。"><?php if(isset($review)) echo $review['secret_comment']; ?></textarea>
  </div>

  <div>
    <button class="btn btn-primary" type="submit" onclick="$('#puzzle_id').val() = $('#puzzle_id').val().split(':')[0]; return true;">投稿する</button>
  </div>

</form>

