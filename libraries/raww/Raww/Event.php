<?php

namespace Raww;


class Event {
  
  protected $events = array();

  /**
  * ...
  *
  */ 
  public function register($event,$callback,$identifier=null){
    
    if(!isset($this->events[$event])) $this->events[$event] = array();
    
    if(!is_null(identifier)){
      $this->events[$event][$identifier] = $callback;
    }else{
      $this->events[$event][] = $callback;
    }
    
  }
  
  /**
  * ...
  *
  */ 
  public function trigger($event,$params=array()){
    
    if(!isset($this->events[$event])){
        return false;
    }
    
    foreach($this->events[$event] as $id => $action){
      if(Utils::is_callback($action)){
          call_user_func_array($action, $params);
      }
    }
    
    return true;
    
  }
  
  /**
  * ...
  *
  */ 
  public function detach($event,$identifier=null){
    
    if(!isset($this->events[$event])){
        return false;
    }
    
    if(!is_null($identifier)){
      if(!isset($this->events[$event][$identifier])){
        return false;
      }else{
        unset($this->events[$event][$identifier]);
        return true;
      }
    }else{
      unset($this->events[$event]);
      return true;
    }
    
  }
  
}