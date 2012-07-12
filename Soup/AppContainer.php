<?php

namespace Soup;

/**
 * AppContainer class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class AppContainer implements \ArrayAccess {
    
	/**
	 * @var $app	Soup App instance
	 */
    public $app;
    
	protected $_container = array();
	
	/**
	 * Class Constructor.
	 *
	 * @param	object $app	Soup App instance
	 * @return	void
	 */
	public function __construct($app){
	
		$this->_container = array();
		$this->app        = $app;
	}
	
	// ArrayAccess implementation
	
	public function offsetSet($offset, $value) {
        $this->_container[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->_container[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->_container[$offset]);
    }
    public function offsetGet($offset) {
        
		if (!isset($this->_container[$offset])) {
            
			if(strpos($offset,'app:')!==false){
				return $this->app[substr($offset,4)];
			}
			
			throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $offset));
        }
		
		return is_callable($this->_container[$offset]) ? call_user_func($this->_container[$offset], $this) : $this->_container[$offset];
    }
}