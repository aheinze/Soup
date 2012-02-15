<?php

namespace Soup;

/**
 * Dependency Injection class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class DI implements \ArrayAccess{
    
    protected $_container = array();
    
	/**
	 * Class Constructor.
	 *
	 * @return	void
	 */
	public function __construct(){
	
		$this->_container = array();
	}

	/**
     * Returns a closure that stores the result of the given closure for
     * uniqueness in the scope of this instance of Pimple.
     *
     * @param Closure $callable A closure to wrap for uniqueness
     * @return Closure The wrapped closure
     */
	public function share($callable) {
        return function ($app) use ($callable) {
            
			static $object;

            if (is_null($object)) {
                $object = $callable($app);
            }

            return $object;
        };
    }
	
    /**
     * Protects a callable from being interpreted as a service.
     * This is useful when you want to store a callable as a parameter.
     *
     * @param Closure $callable A closure to protect from being evaluated
     * @return Closure The protected closure
     */
	public function protect(Closure $callable) {
        return function ($c) use ($callable) {
            return $callable;
        };
	}

    public function self_share($name, $callable) {
        $this[$name] = $this->share($callable);
    }

    public function self_protect($name, $callable) {
        $this[$name] = $this->protect($callable);
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