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
    
    public function set($key, $value) {
        
        $this->_storage[$key] = $value;
    }
    
    public function get($key, $default=null) {
        
        return isset($this->_storage[$key]) ? $this->_storage[$key] : $default;
    }    
    
    public function remove($key) {
        
        if(isset($this->_storage[$key])) unset($this->_storage[$key]);
    }
    
    public function has($key) {
        
        return isset($this->_storage[$key]);
    }
}