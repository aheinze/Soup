<?php

namespace Raww;

class Cache{
  
  protected static $engine = 'Raww\Cache\File';
  
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
	public static function write($key, $value, $duration = 0){
    CacheEngine::get(self::$engine)->write($key, $value, $duration);
  }
  /**
  * ...
  *
  */ 
	public static function read($key, $default=null){
    return CacheEngine::get(self::$engine)->read($key, $default);
  }
  /**
  * ...
  *
  */ 
	public static function delete($key){
    CacheEngine::get(self::$engine)->delete($key);
  }
  /**
  * ...
  *
  */ 
	public static function clear(){
    CacheEngine::get(self::$engine)->clear();
  }
}

class CacheEngine{
  
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