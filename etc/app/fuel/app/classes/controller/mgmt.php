<?php

class Controller_Mgmt extends Controller
{
    public function action_index()
    {
	return View::forge('mgmt/index');
    }
}

