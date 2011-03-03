<?php

namespace Raww;

class I18n {

  public static $locale      = "en";
  protected static $engine   = 'I18nArrayEngine';

  /**
  * ...
  *
  */ 
	public static function get($key, $alternative=null){
    return I18nEngine::get(self::$engine)->get($key, $alternative);
	}
  
  /**
  * ...
  *
  */ 
	public static function setLocale($locale){
      self::$locale = $locale;
	}
}


class I18nEngine{
  
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

class I18nArrayEngine{

	private $_languages = array();

  /**
  * ...
  *
  */ 
  public function __construct(){
  
    
  }
  
  /**
  * ...
  *
  */ 
	public function get($key, $alternative=null){
    
    if(is_null($alternative)){
      $alternative = '{'.$key.'}';
    }
    
    if(!isset($this->_languages[I18n::locale])){
      
      include(Path::get("locale:".I18n::locale.'/table.php');
      
      if(isset($t)) $this->_languages[I18n::locale] = $t;
    }
    
    return isset($this->_languages[I18n::locale][$key]) ? $this->_languages[I18n::locale][$key]:$alternative;
	}

  
}