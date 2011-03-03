<?php

namespace Raww;


class Controller {
    
    public $name       = null;
    public $request    = null;
    public $params     = array();
    
    /**
    * Callback functions
    *
    */
    public function before_filter(){
        return true;
    }

    public function after_filter(){
        return true;
    }

    protected function before_render(){
        
    }

    protected function after_render(){
        
    }

    public function index(){

    }
    
    public function __construct(){

        $this->name = get_class($this);

    }

    protected function db($connection = "default") {
        return \Raww\Connection::get($connection);
    }
    
    protected function render($view=null, $slots=array(), $options=array()){
        
        $response = new Response("", $options);
        
        if (strpos($view, ':') === false ) {
			$view = "modules:$view";
		}
        
        if($path = Path::get($view)){
            $response->body = template($view, $slots);
        } else {
            $response->body = "$view not found!";
        }
        
        return $response;
    }
    
    function redirect($url) {
        Router::reroute($url);
    }
}