<?php

namespace Raww;

class I18n extends AppContainer {
	
	public $locale      = "en";
	private $_languages = array();
	
	public function __construct($app){
		parent::__construct($app);
		
		$this->locale = $app['request']->getClientLang()
	}
	
	public function get($key, $alternative=null){
    
		if(is_null($alternative)){
		  $alternative = '{'.$key.'}';
		}
		
		if(!isset($this->_languages[$this->locale])){

		  if($path = $this->app["path"]->get("locale:".$this->locale.'/table.php')){
			  include($path);
			  
			  if(isset($t)) $this->_languages[$this->locale] = $t;
		  }
		}
		
		return isset($this->_languages[$this->locale][$key]) ? $this->_languages[$this->locale][$key]:$alternative;
	}
}