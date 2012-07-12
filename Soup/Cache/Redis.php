<?php

namespace Soup\Cache;


class Redis extends \Soup\AppContainer {
  
  protected $redis = null;

  /**
  * ...
  *
  */ 
  public function __construct($app, $redis=null){
    parent::__construct($app);

    if (!extension_loaded('redis')){
      throw new \Exception('PHP Redis extension is not available.');
    }

    $this->redis = $redis;

  }

  public function setRedis($redis){
    $this->redis = $redis;
  }

  /**
  * ...
  *
  */ 
	public function write($key, &$value, $duration = -1){
    
    $expire = ($duration==-1) ? -1:(time() + (is_string($duration) ? strtotime($duration)-time():$duration));
    
    $safe_var = serialize(array(
      'expire' => $expire,
      'value' => serialize($value)
    ));

    if($expire==-1){
      return $this->redis->set($key, $safe_var);
    } else {
      return $this->redis->setex($key, $expire, $safe_var);
    }
  }
  
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){
    $var = $this->redis->get($key);

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
    return $this->redis->delete($key);
  }
  
  public function clear(){
    return $this->redis->flushDB();
  }
}