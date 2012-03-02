<?php

namespace App\Controller;


class Start extends \Soup\Controller {

	public $layout = "views:default.php";

    public function index(){
		
    	$redis = $this->db("redis");

    	$redis->set('key232', 45);

    	var_dump($redis->get("key232"));

        return $this->render(__DIR__."/../views/Start/index.php");
    }

}