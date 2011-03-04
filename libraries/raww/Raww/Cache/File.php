<?php

namespace Raww\Cache;


class File {
  
  protected $cachePath = null;

  /**
  * ...
  *
  */ 
  public function __construct(){
  
    $this->cachePath = \Raww\Path::get("cache:");
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