<?php

class Controller_Chart extends Controller_Rest
{

    // 時系列ランキンググラフのデータをダウンロードする
    public function get_ranking()
    {
	$chart_data = Model_Score::get_ranking_chart();
	return $this->response($chart_data);
    }


    // ユーザプロフィールグラフのデータをダウンロードする
    public function get_profile($username)
    {
	// CTF開始前は許可しない
	Controller_Score::checkCTFStatus(true, false);
        // 認証済みユーザのみ許可
        Controller_Auth::redirectIfNotAuth();

	$chart_data = Model_Score::get_profile_chart($username);
	return $this->response($chart_data);
    }
}
