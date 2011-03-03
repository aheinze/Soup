<?php

namespace Raww;

class Session{
  
  protected static $engine = 'SessionPhpEngine';
  
  /**
  * ...
  *
  */ 
  public static function setEngine($engine){
   self::$engine = $engine;
  }
  /**
  * ...
  *
  */ 
  public static function init($engine=null){
   
   if(!is_null($engine)) self::setEngine($engine);
   
   SessionEngine::get(self::$engine)->init();
  }
  /**
  * ...
  *
  */ 
	public static function write($key, $value, $duration = 0){
    SessionEngine::get(self::$engine)->write($key, $value, $duration);
  }
  /**
  * ...
  *
  */ 
	public static function read($key, $default=null){
    return SessionEngine::get(self::$engine)->read($key, $default);
  }
  /**
  * ...
  *
  */ 
	public static function delete($key){
    SessionEngine::get(self::$engine)->delete($key);
  }
  /**
  * ...
  *
  */ 
	public static function clear(){
    SessionEngine::get(self::$engine)->clear();
  }
  /**
  * ...
  *
  */ 
	public static function destroy(){
    SessionEngine::get(self::$engine)->destroy();
  }
}

class SessionEngine{
  
  protected static $__engines = array();
  /**
  * ...
  *
  */ 
  public static function get($engine){
    
    if(!isset(self::$__engines[$engine])){
      self::$__engines[$engine] = new $engine();
    }
    
    return self::$__engines[$engine];
  }
  
}

class SessionPhpEngine{
  /**
  * ...
  *
  */ 
  public function __construct(){
  
    
  }
  /**
  * ...
  *
  */ 
  public function init(){
    session_name(Registry::get("session.name", "rawwapp"));
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