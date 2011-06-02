<?php

namespace Raww;

/**
 * Router class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Router extends AppContainer {
    

	/* routes */
	protected $_routes = array();
    
	public function bind($path, $callback, $condition = true) {
		
        if (!$condition) return;
        
		if (!isset($this->_routes[$path])) {
			$this->_routes[$path] = array();
		}
		
		$this->_routes[$path] = $callback;
	}


	public function dispatch($path) {
             
        $found  = false;
        $params = array();
        
        if (isset($this->_routes[$path])) {
            
            $found = self::render($path, $params);

        } else {
                
            foreach ($this->_routes as $route => $callback) {
                
                $params = array();
                
                /* e.g. #\.html$#  */
                if(substr($route,0,1)=='#' && substr($route,-1)=='#'){
                    
                    if(preg_match($route,$path, $matches)){
                        $params[':captures'] = array_slice($matches, 1);
                        $found = self::render($route, $params);
                        break;
                    }
                }
                
                /* e.g. /admin/*  */
                if(strpos($route, '*') !== false){
                    
                    $pattern = '#'.str_replace('\*', '(.*)', preg_quote($route, '#')).'#';
                    
                    if(preg_match($pattern, $path, $matches)){
                    
                        $params[':splat'] = array_slice($matches, 1);
                        $found = self::render($route, $params);
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
                            $found = self::render($route, $params);;
                            break;
                        }
                    }
                }
            }         
        }
		
        if(!$found){
            $found = self::invokeController($path);
        }
        
        if($found && !is_object($found)) {
            $found = new Response($found);
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
                $ret = $this->invokeController($this->_routes[$route]);
            }
            
			if( !is_null($ret) ){
				return $ret;
			}
			
        }
        
        return $output;
    }
    

    public function baseurl($path) {
		
		$path = '/'.ltrim($path, '/');
		
        return $this->app["base_url_path"].$path;
    }
	
    public function url($path) {
		
		$path = '/'.ltrim($path, '/');
		
        return $this->app["base_route_path"].$path;
    }
    

    public function reroute($path) {
    
        if (strpos($path,'://') === false) {
          if(substr($path,0,1)!='/'){
            $path = '/'.$path;
          }
          $path = $this->url($path);
        }

        header('Location: '.$path);
        exit;
    }
    
    
	public function invokeController($path, $params=array()) {
		
        $parsedUri = array(
            'module'     => 'App',
            'controller' => 'App',
            'action'     => 'index',
            'params'     => $params
        );
        
        if(is_array($path)){
            
            $parsedUri = array_merge($parsedUri, $path);
        
        } else {
            $parts = explode('/', trim(trim($path), '/'));
            
            //check for module
            //-----------------------------------------------------
            if($this->app["path"]->get("modules:".ucfirst($parts[0]))){

              $parsedUri['module'] = ucfirst($parts[0]);
              $parts               = array_slice($parts,1);

              switch(count($parts)){
                case 0:
                  $parts[0] = $parsedUri['module'];
                  break;
                case 1:
                case 2:

                  if(!$this->app["path"]->get("modules:".$parsedUri['module'].'/Controller/'.ucfirst($parts[0]).'.php')){
                    array_unshift($parts, $parsedUri['module']);
                  }
                  
                  break;
              }
            }
            //-----------------------------------------------------

            switch(count($parts)) {
                case 1:
                    if($parts[0]!=='') $parsedUri['controller'] = ucfirst($parts[0]);
                    break;
                case 2:
                    $parsedUri['controller'] = ucfirst($parts[0]);
                    $parsedUri['action']     = ucfirst($parts[1]);
                break;
                default:
                    $parsedUri['controller'] = ucfirst($parts[0]);
                    $parsedUri['action']     = $parts[1];
                    $parsedUri['params']     = array_slice($parts,2);
            }
            
        }

        $controllerName = $parsedUri['module'].'\\Controller\\'.$parsedUri['controller']; 
     
        if(!class_exists($controllerName)){
            
            if($controllerFile = $this->app["path"]->get("modules:".$parsedUri['module'].'/Controller/'.$parsedUri['controller'].'.php')){
                require_once($controllerFile);   
            }
        }
       
        if(!class_exists($controllerName)){
            return false;
        }
        
        $controller = new $controllerName($this->app);
        
        if(!method_exists($controller, $parsedUri['action'])){
          if(method_exists($controller, 'index')) {
			$parsedUri['params'] = array_merge(array($parsedUri['action']), $parsedUri['params']);
			$parsedUri['action'] = 'index';
		  }else{
			return false;
		  }
        }
        
        $controller->invoked_action = $parsedUri['action'];
        
        if(!$controller->before_filter()) return false;
        
        $return = call_user_func_array(array(&$controller, $parsedUri['action']), $parsedUri['params']);
        
        if(!$controller->after_filter()) return false;
        
        return $return;
	}


}