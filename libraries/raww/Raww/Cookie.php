<?php

namespace Raww;

class Cookie {

	public static $expiration = 0;
	public static $path = '/';
	public static $domain = null;
	public static $secure = false;
	public static $httponly = false;

	public static function get($name, $default = null) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
	}

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

	public static function delete($name) {

		unset($_COOKIE[$name]);

		return static::set($name, null, -86400);
	}
  
}