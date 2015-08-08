<?php

class Controller_Chart extends Controller_Rest
{

    public function get_list()
    {
	$chart_data = Model_Score::get_chart_data();
	return $this->response($chart_data);
    }

}
