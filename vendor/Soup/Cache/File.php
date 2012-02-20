<?php

namespace Soup\Cache;


class File extends \Soup\AppContainer {
  
  protected $cachePath = null;

  /**
  * ...
  *
  */ 
  public function __construct($app){
    parent::__construct($app);
    $this->cachePath = rtrim($app['path']->get("cache:"),"/\\")."/";
  }

  /**
  * ...
  *
  */ 
  public function cachePath($path=false){
    if($path){
      $this->cachePath = rtrim($path, "/\\")."/";
    }

    return $this->cachePath;
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
    
    file_put_contents($this->cachePath.md5($key).".cache" , serialize($safe_var));
  }
  
  /**
  * ...
  *
  */ 
	public function read($key, $default=null){
    $var = @file_get_contents($this->cachePath.md5($key).".cache");

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
  
  /**
  * ...
  *
  */
  public function clear(){
    
    $iterator =  new \RecursiveDirectoryIterator($this->cachePath);

    foreach($iterator as $file) {      
       if($file->isFile()) {
          @unlink($this->cachePath.$file->getFilename());
       }
    }
  }
}