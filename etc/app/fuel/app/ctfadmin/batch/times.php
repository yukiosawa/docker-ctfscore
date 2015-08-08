<?php
/* (1) DBに登録する時刻をこのファイルに記述する */
/* (2) 以下を実行する。
       php oil r ctfscore:update_times path_to_this_file */
/* (3) 時刻はMySQL DATETIME型で記述する。yyyy-mm-dd hh:mm:ss */


$times = array(
    'start_time' => '2014-10-30 09:00:00',
    'end_time'   => '2014-10-31 18:00:00',
);

