<?php

namespace App\Controller;

class Start extends \Raww\Controller {

    public function index(){
    
        return $this->render("App/views/Start/index.php");
    }

}