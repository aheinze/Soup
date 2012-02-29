<?php

namespace Soup;

/**
 * Cookie class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Cookie {

	public static $expiration = 0;
	public static $path = '/';
	public static $domain = null;
	public static $secure = false;
	public static $httponly = false;

	/**
	 * Get value of $_COOKIE array 
	 *
	 * @param	string $name	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function get($name, $default = null) {
		return Request::cookie($name, $default);
	}

	/**
	 * Set key/value of $_COOKIE array 
	 *
	 * @param	string $name	
	 * @param	string $value	
	 * @param	int $expiration	
	 * @param	string $path	
	 * @param	string $domain	
	 * @return	boolean
	 */
	public static function set($name, $value, $expiration = null, $path = null, $domain = null) {
		
	    if ($expiration === null) {
	        $expiration = time() + 86500;
	    }else{
			$expiration = $expiration > 0 ? $expiration + time() : 0;
		}

		if (empty($path))  $path = static::$path;
		if (empty($domain)) $domain = static::$domain;

		return setcookie($name, $value, $expiration, $path, $domain, static::$secure, static::$httponly);
	}

	/**
	 * Delete $_COOKIE setting 
	 *
	 * @param	string $name	Index
	 * @return	boolean
	 */
	public static function delete($name) {

		unset($_COOKIE[$name]);
		unset(Request::$_COOKIE[$name]);

		return static::set($name, null, -86400);
	}
}