<?php

namespace Soup;

/**
 * Controller class. Handle requests
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Controller extends AppContainer {
    
    public $invoked_action = null;
	
    public $name       = null;
    public $layout     = null;
    
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

    protected function db($name = "default") {
		return $this->app["storage:$name"];
    }
    
    protected function render($view=null, $slots=array(), $options=array()){
        
        $render   = $view;
        $response = new Response(null, $options);
		        
        if (strpos($view, ' with ')===false && $this->layout) {
			$render = "$view with ".$this->layout;
		}

        if (strpos($render, ' with ')!==false) {
            list($view, $layout) = explode(' with ', $render);
        }
        
        if($path = $this->app["path"]->get($view)){
            $response->body = $this->app["view"]->render($render, $slots);
        } else {
            $response->body = "$view not found!";
        }
        
        return $response;
    }
    
    protected function redirect($url) {
        $this->app["router"]->reroute($url);
    }
	
	protected function get($index=null, $default = null) {
		return $this->app['request']->get($index, $default);
	}
	protected function post($index=null, $default = null) {
		return $this->app['request']->post($index, $default);
	}
	protected function put($index=null, $default = null) {
		return $this->app['request']->put($index, $default);
	}
	protected function delete($index=null, $default = null) {
		return $this->app['request']->delete($index, $default);
	}
}