<?php

namespace Raww\Session;


class Php extends \Raww\AppContainer {
  
  /**
  * ...
  *
  */ 
  public function init(){
    
	if(strlen(session_id())) {
		session_destroy();
	}
	
	session_name($this->app->get("session.name", $app->name()));
    session_start();
  }

  /**
  * ...
  *
  */ 
	public function write($key, &$value){
    
    $keys = explode('.',$key);
    
    switch(count($keys)){
      
      case 1:
        $_SESSION[$keys[0]] = $value;
        break;
      
      case 2:
        $_SESSION[$keys[0]][$keys[1]] = $value;
        break;
      
      case 3:
        $_SESSION[$keys[0]][$keys[1]][$keys[2]] = $value;
        break;
        
      case 4:
        $_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
        break;
    }
    
  }
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){

    $keys = explode('.',$key);
    
    switch(count($keys)){
      
      case 1:
        if(isset($_SESSION[$keys[0]])){
          return $_SESSION[$keys[0]];
        }
        break;
      
      case 2:
        if(isset($_SESSION[$keys[0]][$keys[1]])){  
          return $_SESSION[$keys[0]][$keys[1]];
        }
        break;
      
      case 3:
        if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]])){
          return $_SESSION[$keys[0]][$keys[1]][$keys[2]];
        }
        break;
        
      case 4:
        if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
          return $_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]];
        }
        break;
    }
    
    return $default;
  }
  /**
  * ...
  *
  */ 
	public function delete($key){
    $keys = explode('.',$key);
       
    switch(count($keys)){
      
      case 1:
        if(isset($_SESSION[$keys[0]])){
          unset($_SESSION[$keys[0]]);
        }
        break;
      
      case 2:
        if(isset($_SESSION[$keys[0]][$keys[1]])){  
          unset($_SESSION[$keys[0]][$keys[1]]);
        }
        break;
      
      case 3:
        if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]])){
          unset($_SESSION[$keys[0]][$keys[1]][$keys[2]]);
        }
        break;
        
      case 4:
        if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
          unset($_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]]);
        }
        break;
    }
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
}