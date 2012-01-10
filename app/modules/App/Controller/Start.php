<?php

namespace App\Controller;


class Start extends \Raww\Controller {

	public $layout = "views:default.php";

    public function index(){
		
        return $this->render("modules:App/views/Start/index.php");
    }

}