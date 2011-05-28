<?php

namespace Raww;

class DI implements \ArrayAccess{
    
    protected $_container = array();
    
	public function __construct(){
	
		$this->_container = array();
	}

	public function share($callable) {
        return function ($app) use ($callable) {
            
			static $object;

            if (is_null($object)) {
                $object = $callable($app);
            }

            return $object;
        };
    }
	
	public function protect(Closure $callable) {
        return function ($c) use ($callable) {
            return $callable;
        };
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
            throw new \InvalidArgumentException(sprintf('Identifier "%s" is not defined.', $offset));
        }
		
		return is_callable($this->_container[$offset]) ? call_user_func($this->_container[$offset], $this) : $this->_container[$offset];
    }
}