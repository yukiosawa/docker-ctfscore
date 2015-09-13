<?php

class Model_Review extends Model
{
    // レビュー投稿可能な問題すべてを取得
    public static function get_reviewable_puzzles($uid)
    {
        // 回答済のチェック
        if (!Config::get('ctfscore.review.allow_unanswered_puzzle'))
        {
            return Model_Puzzle::get_answered_puzzles($uid);
        }
	else
	{
	    return Model_Puzzle::get_puzzles();
	}
    }


    // 編集権限のあるレビューを取得
    public static function get_editable_review($review_id)
    {
	$review = null;
	$reviews = Model_Review::get_reviews($review_id, null, null, true);
	if ($reviews)
	{
	    $review = $reviews[0];
	}
	else
	{
	    // レビューIDが無効
	    return null;
	}

	// 管理者ログインの場合は無条件に権限付与
	if (Controller_Auth::is_admin())
	{
	    return $review;
	}

	// 回答済のチェック
	list($driver, $userid) = Auth::get_user_id();
        if (!Config::get('ctfscore.review.allow_unanswered_puzzle'))
        {
	    $puzzle_id = $review['puzzle_id'];
	    if (!Model_Puzzle::is_answered_puzzle($userid, $puzzle_id))
	    {
		return null;
	    }
	}

	// 自分が投稿したレビュー
	if ($userid == $review['uid'])
	{
	    return $review;
	}
	else
	{
	    return null;
	}
    }


    public static function get_reviews($review_id = null, $puzzle_id = null, $uid = null, $admin = false)
    {
	if ($admin)
	{
	    // 全データを取得
	    $query = DB::select(
		array('reviews.id', 'id'),
		array('reviews.puzzle_id', 'puzzle_id'),
		array('reviews.score', 'score'),
		array('reviews.comment', 'comment'),
		array('reviews.secret_comment', 'secret_comment'),
		array('reviews.uid', 'uid'),
		array('reviews.updated_at', 'updated_at'),
		array('users.username', 'username')
	    )->from('reviews');
	}
	else
	{
	    // 管理者用データ(secret_comment)は取得しない
	    $query = DB::select(
		array('reviews.id', 'id'),
		array('reviews.puzzle_id', 'puzzle_id'),
		array('reviews.score', 'score'),
		array('reviews.comment', 'comment'),
		array('""', 'secret_comment'),
		array('reviews.uid', 'uid'),
		array('reviews.updated_at', 'updated_at'),
		array('users.username', 'username')
	    )->from('reviews');
	}
	
	if (!is_null($review_id))
	{
	    $query->where('reviews.id', $review_id);
	}
	if (!is_null($puzzle_id))
	{
	    $query->where('puzzle_id', $puzzle_id);
	}
	if (!is_null($uid))
	{
	    $query->where('uid', $uid);
	}

	$query->join('users', 'LEFT')
	      ->on('reviews.uid', '=', 'users.id')
	      ->order_by('reviews.updated_at', 'desc');
	$result = $query->execute()->as_array();

	// 問題タイトルを付加
	foreach ($result as &$review)
	{
	    $puzzle = Model_Puzzle::get_puzzles($review['puzzle_id']);
	    if($puzzle)
	    {
		$review['puzzle_title'] = $puzzle[0]['title'];
	    }
	    else
	    {
		$review['puzzle_title'] = '';
	    }
	}
	unset($review);

	return $result;
    }


    public static function create_review($puzzle_id, $score, $comment, $secret_comment, $uid)
    {
	// 問題IDの存在チェック
	if (!Model_Puzzle::get_puzzles($puzzle_id)) return null;

        // 回答済のチェック
	if (!Config::get('ctfscore.review.allow_unanswered_puzzle'))
	{
            if (!Model_Puzzle::is_answered_puzzle($uid, $puzzle_id)) return null;
	}

	$id = '';
	$now = Model_Score::get_current_time();

	try
	{
	    DB::start_transaction();
	    $result = DB::insert('reviews')->set(array(
		'puzzle_id' => $puzzle_id,
		'score' => $score,
		'comment' => $comment,
		'secret_comment' => $secret_comment,
		'uid' => $uid,
		'updated_at' => $now
	    ))->execute();
	    DB::commit_transaction();
	    // INSERT実行の戻り値は
	    // return array(
	    //     lastInsertedId, // AUTO_INCREMENTなフィールドにセットされたID
	    //     rowCount // 挿入された行数
	    // );
	    $id = $result[0];
	}
	catch (Exception $e)
	{
	    DB::rollback_transaction();
	    throw $e;
	}
	
	return $id;
    }


    public static function update_review($id, $puzzle_id, $score, $comment, $secret_comment, $uid)
    {
	// 問題IDの存在チェック
	if (!Model_Puzzle::get_puzzles($puzzle_id)) return null;

	// 編集権のチェック
	if (!Model_Review::get_editable_review($id)) return null;
	
	$result = '';
	$now = Model_Score::get_current_time();
	
	try
	{
	    DB::start_transaction();
	    $result = DB::update('reviews')->set(array(
		'puzzle_id' => $puzzle_id,
		'score' => $score,
		'comment' => $comment,
		'secret_comment' => $secret_comment,
		'uid' => $uid,
		'updated_at' => $now
	    ))->where('id', $id)->execute();
	    DB::commit_transaction();
	}
	catch (Exception $e)
	{
	    DB::rollback_transaction();
	    throw $e;
	}
	return $result;
    }


    public static function delete_review($id)
    {
	// 編集権のチェック
	if (!Model_Review::get_editable_review($id)) return null;

	return DB::delete('reviews')->where('id', $id)->execute();
    }


    public static function average_score($puzzle_id)
    {
	$result = DB::select(DB::expr('AVG(score)'))->from('reviews')
			     ->where('puzzle_id', $puzzle_id)
			     ->execute()->as_array();
	return $result[0]['AVG(score)'];
    }


    // ブラウザからの入力パラメータのチェック
    public static function validate($factory)
    {
	$val = Validation::forge($factory);

	if ($factory == 'create' || $factory == 'edit')
	{
	    $val->add('puzzle_id', '問題番号')
		->add_rule('required')
		->add_rule('numeric_max', 255)
		->add_rule('numeric_min', 1);
	    $val->add('score', '評価点')
		->add_rule('required')
		->add_rule('numeric_max', 10)
		->add_rule('numeric_min', 0);
	    $val->add('comment', '公開コメント')
		->add_rule('max_length', 255);
	    $val->add('secret_comment', '管理者へのメッセージ')
		->add_rule('max_length', 255);
	}
	else if ($factory == 'delete')
	{
	    $val->add('review_id', 'レビューID')
		->add_rule('required')
		->add_rule('numeric_max', 255)
		->add_rule('numeric_min', 1);
	}

	return $val;
    }
}

