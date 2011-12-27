<?php

namespace Raww\Session;


class Php extends \Raww\AppContainer {
  
  /**
  * ...
  *
  */ 
  public function init($sessionname=null){
    
	if(strlen(session_id())) {
		session_destroy();
	}
	
	session_name($sessionname ? $sessionname : $this->app->name());
    session_start();
  }

  /**
  * ...
  *
  */ 
	public function write($key, $value){
    
		$_SESSION[$key] = $value;
	}
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){

		return \Raww\fetch_from_array($_SESSION, $key, $default);
	}
  /**
  * ...
  *
  */ 
	public function delete($key){
    
	unset($_SESSION[$key]);
	}
  /**
  * ...
  *
  */ 
	public function clear(){
  
	}
  /**
  * ...
  *
  */ 
	public function destroy(){
		session_destroy();
	}
  
	// ArrayAccess implementation
	
	public function offsetSet($offset, $value) {
        $_SESSION[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($_SESSION[$offset]);
    }
    public function offsetUnset($offset) {
        unset($_SESSION[$offset]);
    }
    public function offsetGet($offset) {
        
		$value = \Raww\fetch_from_array($_SESSION, $offset, null);
		
		return $value;
    }
}