<?php

namespace Raww;

/**
 * I18n class. Manage translations
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class I18n extends AppContainer {
	
	/**
	 * @var $locale	current language
	 */
	public $locale      = "en";
	private $_languages = array();
	
	/**
	 * Class Constructor.
	 *
	 * @param	object $app	Raww App instance
	 * @return	void
	 */
	public function __construct($app){
		parent::__construct($app);
		
		$this->locale = $app['request']->getClientLang();
	}
	
	/**
	 * Get translated string by key
	 *
	 * @param	string $key	translation key
	 * @param	array $alternative	returns if $key doesn''t exist
	 * @return	string
	 */
	public function get($key, $alternative=null){
    
		if(is_null($alternative)){
		  $alternative = $key;
		}
		
		if(!isset($this->_languages[$this->locale])){

		  if($path = $this->app["path"]->get("locale:".$this->locale.'/table.php')){
			
			$this->_languages[$this->locale] = include($path);
		  }
		}
		
		return isset($this->_languages[$this->locale][$key]) ? $this->_languages[$this->locale][$key]:$alternative;
	}
}