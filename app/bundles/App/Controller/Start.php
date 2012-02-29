<?php

namespace Bundle\App\Controller;


class Start extends \Soup\Controller {

	public $layout = "views:default.php";

    public function index(){
		
        return $this->render("bundles:App/views/Start/index.php");
    }

}