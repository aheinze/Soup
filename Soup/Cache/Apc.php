<?php

namespace Soup\Cache;


class Apc extends \Soup\AppContainer {
  
  /**
  * ...
  *
  */ 
  public function __construct($app){
    parent::__construct($app);
    
    if (!extension_loaded('apc')){
      throw new \Exception('PHP APC extension is not available.');
    }
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

    return apc_store($key, serialize($safe_var), $expire);
  }
  
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){
    $var = apc_fetch($key, $success);

    if(!$success){
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
    return apc_delete($key);
  }
  
  public function clear(){
    return apc_clear_cache('user');
  }
}