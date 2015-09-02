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

  <div class="col-md-offset-1 col-md-6">
  <?php if ($show_my_answered): ?>
    <div id="myscore">
      <table class="table table-condensed table-hover">
	<thead>
	  <tr class="success">
	    <th>番号</th><th>回答済みの答え</th><th>ポイント</th>
	  </tr>
	</thead>
	<tbody>
	  <?php
	  foreach ($my_answered as $ans) {
	    echo "<tr>";
	    echo "<td>" . $ans['flag_id'] . "</td>";
	    echo "<td>" . $ans['flag'] . "</td>";
	    echo "<td>" . $ans['point'] . "</td>";
	    echo "</tr>";
	  }
	  ?>
	</tbody>
      </table>
    </div>
  <?php endif; ?>
  </div>
</div>

<?php endif; ?>


<p>
<div id="ranking">
  <!-- <div class="row">
       <div class="col-md-6 ">
       <span class="h4">総合ランキング</span>
       <span><button class="btn btn-primary" onclick="location.reload()">更新する</button></span>
       </div>
       </div> -->

  <div class="row">
    <div class="col-md-12">
    <table class="table table-condensed table-hover">
      <thead>
	<tr class="success">
	  <th>ランク</th><th>ユーザ</th><th>ポイント</th><th>更新時刻</th>
	  <?php
	  if ($show_all_answered_detail) {
	      foreach ($all_flags as $flag){
		  echo "<th width='30'>" . $flag['flag_id'] . "</th>";
	      }
	  }
	  ?>
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
	  echo "<td>" . $score['username'] . "</td>";
	  echo "<td>" . $score['totalpoint'] . "</td>";
	  echo "<td>" . $score['pointupdated_at'] . "</td>";
	    if ($show_all_answered_detail) {
	  foreach ($all_flags as $flag) {
	    /* 各フラグの回答状況 */
	    if ($score['flags'][$flag['flag_id']] == 'done') {
	      echo "<td><span class='glyphicon glyphicon-ok'></span></td>";
	    } else {
	      echo "<td></td>";
	    }
	  }
		}
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

