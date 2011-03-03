<?php

namespace Raww;


class Event {
  
  protected static $events = array();

  /**
  * ...
  *
  */ 
  public static function register($event,$callback,$identifier=null){
    
    if(!isset(self::$events[$event])) self::$events[$event] = array();
    
    if(!is_null(identifier)){
      self::$events[$event][$identifier] = $callback;
    }else{
      self::$events[$event][] = $callback;
    }
    
  }
  
  /**
  * ...
  *
  */ 
  public static function trigger($event,$params=array()){
    
    if(!isset(self::$events[$event])){
        return false;
    }
    
    foreach(self::$events[$event] as $id => $action){
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
  public static function detach($event,$identifier=null){
    
    if(!isset(self::$events[$event])){
        return false;
    }
    
    if(!is_null($identifier)){
      if(!isset(self::$events[$event][$identifier])){
        return false;
      }else{
        unset(self::$events[$event][$identifier]);
        return true;
      }
    }else{
      unset(self::$events[$event]);
      return true;
    }
    
  }
  
}