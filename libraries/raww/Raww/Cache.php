<?php

namespace Raww;

class Cache{
  
  protected static $engine = 'Raww\CacheFileEngine';
  
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

class CacheFileEngine{
  
  protected $cachePath = null;

  /**
  * ...
  *
  */ 
  public function __construct(){
  
    $this->cachePath = Path::get("cache:");
  }

  /**
  * ...
  *
  */ 
	public function write($key, &$value, $duration = -1){
    
    $expire = ($duration==-1) ? -1:(time() + (is_string($duration) ? strtotime($duration):$duration));
    
    $safe_var = array(
      'expire' => $expire,
      'value' => serialize($value)
    );
    
    file_put_contents($this->cachePath.md5($key) , serialize($safe_var));
  }
  
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){
    $var = @file_get_contents($this->cachePath.md5($key));

    if($var===''){
      return $default;
    }else{
      
      $time = time();
      $var  = unserialize($var);
     
      if(($var['expire'] < $time) && $var['expire']!=-1){
        return $default;
      }

      return unserialize($var['value']);
    }
  }
  
  /**
  * ...
  *
  */ 
	public function delete($key){
    @unlink($this->cachePath.md5($key));
  }
  
	public function clear(){
  
  }
}