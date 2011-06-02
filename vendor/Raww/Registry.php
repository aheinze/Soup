<?php

namespace Raww;

/**
 * Registry class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Registry extends AppContainer {
    
    protected $_storage = array();
	
	/**
	 * Set key/value
	 *
	 * @param	string $key	
	 * @param	mixed $value	
	 * @return	void
	 */
    public function set($key, $value) {
        
        $this->_storage[$key] = $value;
    }
    
	/**
	 * Get value by key
	 *
	 * @param	string $key	
	 * @param	mixed $default	
	 * @return	mixed
	 */
    public function get($key, $default=null) {
        
        return isset($this->_storage[$key]) ? $this->_storage[$key] : $default;
    }    
    
	/**
	 * Remove key
	 *
	 * @param	string $key		
	 * @return	void
	 */
    public function remove($key) {
        
        if(isset($this->_storage[$key])) unset($this->_storage[$key]);
    }
    
	/**
	 * Check if key exists
	 *
	 * @param	string $key		
	 * @return	boolean
	 */
    public function has($key) {
        
        return isset($this->_storage[$key]);
    }
}