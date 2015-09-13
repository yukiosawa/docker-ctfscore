<?php if (!($my_name == '' || $my_name == 'guest')): ?>

<div class="row">
  <div class="col-md-5">
    <div id="send-answer">
      <form class="form-inline" action="/score/submit" method="post">
	<!-- CSRF対策 -->
	<input type="hidden" name="<?php echo \Config::get('security.csrf_token_key');?>" value="<?php echo \Security::fetch_token();?>" />
	<div class="form-group col-md-9">
	  <label class="sr-only" for="answertext">Answer</label>
	  <input class="form-control" id="answertext" name="answer" type="text" placeholder="flag を入力"></input>
	</div>
	<div class="col-md-3">
	  <button class="btn btn-primary" type="submit">回答する</button>
	</div>
      </form>
    </div>
  </div>
</div>

<?php endif; ?>


<p>
<div id="ranking">
  <div class="row">
    <div class="col-md-12">
    <table class="table table-condensed table-hover">
      <thead>
	<tr class="success">
	  <th>ランク</th><th>ユーザ</th><th>ポイント</th><th>更新時刻</th>
	</tr>
      </thead>
      <tbody>
	<?php
	$rank = 1;
	foreach ($scoreboard as $score) {
	    /* 自分の行を強調表示 */
	    if ($my_name == $score['username']) {
		echo "<tr class='danger'>";
	    } else {
		echo "<tr>";
	    }
	    echo "<td>" . $rank . "</td>";
	    echo "<td><a href=/score/profile/" . $score['username'] . ">" . $score['username'] . "</a></td>";
	    echo "<td>" . $score['totalpoint'] . "</td>";
	    echo "<td>" . $score['pointupdated_at'] . "</td>";
	    echo "</tr>\n";
	    $rank++;
	}
	?>
      </tbody>
    </table>
    </div>
  </div>
</div>
</p>

