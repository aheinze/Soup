<?php

namespace Raww;


class Controller extends AppContainer {
    
    public $name       = null;
    public $request    = null;
    public $invoked_action = null;
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
    
    public function __construct($app){
		
		parent::__construct($app);
		
        $this->name = get_class($this);

    }

    protected function db($connection = "default") {
		return isset($this->app["con:$connection"]) ? $this->app["con:$connection"] : null;
    }
    
    protected function render($view=null, $slots=array(), $options=array()){
        
        $response = $this->app["response"]->assign($options);
		        
        if (strpos($view, ':') === false ) {
			$view = "modules:$view";
		}
        
        if($path = $this->app["path"]->get($view)){
            $response->body = $this->app["tpl"]->render($view, $slots);
        } else {
            $response->body = "$view not found!";
        }
        
        return $response;
    }
    
    protected function redirect($url) {
        $this->app["router"]->reroute($url);
    }
}