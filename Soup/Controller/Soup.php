<?php

namespace Soup\Controller;

class Soup extends \Soup\Controller {
	
	public $layout = "views:soup/soup.layout.php";

	public function index(){
		return $this->render("views:soup/index.php");
	}

	public function tests(){
		return $this->render("views:soup/tests.php");
	}

	public function profiler(){
		return $this->render("views:soup/profiler.php");
	}
	
    public function route(){

        $route  = trim(str_replace("/--soup", "", $this->app["route"]), "/");
        $parts  = explode("/", $route);
        $action = method_exists($this, $parts[0]) ? $parts[0] : 'index';

		switch(count($parts)) {
            case 1:
                $params = array();
                break;
            default:
                $params = array_slice($parts,1);
        }

        $controller->invoked_action = $action;

        return call_user_func_array(array(&$this, $action), $params);
    }
}