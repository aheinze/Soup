<?php

namespace Raww;


class Registry {
    
    protected static $_storage = array();
    
    public static function set($key, $value) {
        
        self::$_storage[$key] = $value;
    }
    
    public static function get($key, $default=null) {
        
        return isset(self::$_storage[$key]) ? self::$_storage[$key] : $default;
    }    
    
    public static function remove($key) {
        
        if(isset(self::$_storage[$key])) unset(self::$_storage[$key]);
    }
    
    public static function has($key) {
        
        return isset(self::$_storage[$key]);
    }
}