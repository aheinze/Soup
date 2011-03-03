<?php

namespace Raww;


class Connection {
  
  protected static $connections = array();

  /**
  * ...
  *
  */ 
  public static function register($name,$connection){
    
    self::$connections[$name] = $connection;
    
  }
  
  public static function get($name){
    
    return self::$connections[$name];
    
  } 
  
}