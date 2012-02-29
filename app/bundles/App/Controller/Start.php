<?php

namespace Bundle\App\Controller;


class Start extends \Soup\Controller {

	public $layout = "views:default.php";

    public function index(){
		
        return $this->render(__DIR__."/../views/Start/index.php");
    }

}