<?php

namespace Soup;

/**
 * Router class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Router extends AppContainer {
    

	/* routes */
    protected $_routes     = array();
    protected $_nameindex  = array();
	protected $_autoroutes = array();
    
	public function bind($path, $callback, $condition = true, $name=null) {
		
        if (!$condition) return;

		if (!isset($this->_routes[$path])) {
			$this->_routes[$path] = array();
		}
		
		$this->_routes[$path] = $callback;

        if(!is_null($name)){
            $this->_nameindex[$name] = $path;
        }
	}

    public function auto_route($path_start, $namespace) {

  
        $this->_autoroutes[$path_start] = $namespace;
    }


	public function dispatch($path) {
             
        $found  = false;
        $params = array();
        
        if (isset($this->_routes[$path])) {
            
            $found = $this->render($path, $params);

        } else {
                
            foreach ($this->_routes as $route => $callback) {
                
                $params = array();
                
                /* e.g. #\.html$#  */
                if(substr($route,0,1)=='#' && substr($route,-1)=='#'){
                    
                    if(preg_match($route,$path, $matches)){
                        $params[':captures'] = array_slice($matches, 1);
                        $found = $this->render($route, $params);
                        break;
                    }
                }
                
                /* e.g. /admin/*  */
                if(strpos($route, '*') !== false){
                    
                    $pattern = '#'.str_replace('\*', '(.*)', preg_quote($route, '#')).'#';
                    
                    if(preg_match($pattern, $path, $matches)){
                    
                        $params[':splat'] = array_slice($matches, 1);
                        $found = $this->render($route, $params);
                        break;
                    }
                }
                
                /* e.g. /admin/:id  */
                if(strpos($route, ':') !== false){
                    
                    $parts_p = explode('/', $path);
                    $parts_r = explode('/', $route);
                    
                    if(count($parts_p) == count($parts_r)){
                        
                        $matched = true;
                        
                        foreach($parts_r as $index => $part){
                            if(substr($part,0,1)==':') {
                                $params[substr($part,1)] = $parts_p[$index];
                                continue;
                            }
                            
                            if($parts_p[$index] != $parts_r[$index]) {
                                $matched = false;
                                break;
                            }
                        }
                        
                        if($matched){
                            $found = $this->render($route, $params);;
                            break;
                        }
                    }
                }
            }         
        }
        
        if($found && !is_object($found)) {

            $found = new Response($found);

        }elseif(count($this->_autoroutes)){

            foreach ($this->_autoroutes as $path_start => $namespace) {
                if(strpos($path, $path_start)===0){
                    
                    $parts = explode('/', trim($path, '/'));
                    $controller = '';
                    $action     = 'index';
                    $params     = array();

                    switch(count($parts)){

                        case 1:

                            $controller = $namespace.'\\Controller\\';
                            break;

                        case 2:
                            $controller = $namespace.'\\Controller\\'.ucfirst($parts[1]);
                            break;

                        default:
                            $controller = $namespace.'\\Controller\\'.ucfirst($parts[1]);
                            $action     = $parts[2];

                            if(count($parts)>3){
                                $params = array_slice($parts, 3);
                            }
                    }

                    $result = $this->invoke($controller, $action, $params);

                    if($result && !is_object($result)) {
                        $result = new Response($result);
                    }

                    return $result;
                }
            }
        }
        
        return $found;
	}
    

    public function render($route, $params = array()) {
        
        $output = false;
        
        if(isset($this->_routes[$route])) {
            
            if(is_callable($this->_routes[$route])){
                $ret = call_user_func($this->_routes[$route], $params);
            }
            
            if(is_array($this->_routes[$route]) && isset($this->_routes[$route]['controller'])){
                $ret = $this->invoke($this->_routes[$route]);
            }
            
			if( !is_null($ret) ){
				return $ret;
			}
			
        }
        
        return $output;
    }
    

    public function base_url($path) {
		
		$path = '/'.ltrim($path, '/');
		
        return $this->app["base_url_path"].$path;
    }
	
    public function route_url($path) {
		
		$path = '/'.ltrim($path, '/');
		
        return $this->app["base_route_path"].$path;
    }

    public function named_url($name, $params) {
        
        $route = "";

        if(isset($this->_nameindex[$name])){
            $route = $this->_nameindex[$name];
        }

        $route = '/'.trim($route, '/');

        if(count($params)){
            if(isset($params[0])){
                $route .= '/'.implode('/', $params);
            }else{
                $route = str_replace(array_keys($params), array_values($params), $route);
            }
        }
        
        return $this->app["base_route_path"].$route;
    }
    

    public function reroute($path) {
    
        if (strpos($path,'://') === false) {
          if(substr($path,0,1)!='/'){
            $path = '/'.$path;
          }
          $path = $this->route_url($path);
        }

        header('Location: '.$path);
        exit;
    }
    
    
	public function invoke($controller, $action="index", $params=array()) {
		
        if(is_array($controller)){
            
            extract(array_merge(array(
                "controller" => "",
                "action" => $action,
                "params" => $params
            ), $controller));
        
        }

        $Controller = new $controller($this->app);

        if(!method_exists($Controller, $action)){
          if(method_exists($Controller, 'index')) {
			$action = 'index';
		  }else{
			return false;
		  }
        }
        
        $Controller->invoked_action = $action;
        
        if(!$Controller->before_filter()) return false;
        
        $return = call_user_func_array(array(&$Controller, $action), $params);
        
        if(!$Controller->after_filter()) return false;

        return $return;
	}


}