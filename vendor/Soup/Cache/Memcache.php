<?php

namespace Soup\Cache;


class Memcache extends \Soup\AppContainer {
  
  protected $memcache = null;

  /**
  * ...
  *
  */ 
  public function __construct($app){
    parent::__construct($app);

    if (!extension_loaded('memcache')){
      throw new \Exception('PHP Memcache extension is not available.');
    }

  }

  public function setMemcache($memcache){
    $this->memcache = $memcache;
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

    return $this->memcache->set($key, serialize($safe_var), 0, ($expire==-1) ? 0:$expire);
  }
  
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){
    $var = $this->memcache->get($key);

    if(!$var){
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
    return $this->memcache->delete($key);
  }
  
  public function clear(){
    return $this->memcache->flush();
  }
}