<?php
/* (1) DBに登録するflagをこのファイルに記述する */
/* (2) 以下を実行する。
       php oil r ctfscore:insert_flags path_to_this_file */


$flags = array(
    array(
        "flag_id" => 1,
        "flag" => "sample1",
        "point" => 10,
    ),
    array(
        "flag_id" => 2,
        "flag" => "sample2",
        "point" => 20,
    ),
    array(
        "flag_id" => 3,
        "flag" => "sample3",
        "point" => 20,
    ),
    array(
        "flag_id" => 4,
        "flag" => "sample4",
        "point" => 30,
    ),
    array(
        "flag_id" => 5,
        "flag" => "sample5",
        "point" => 50,
    ),
);

