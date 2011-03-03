<?php

namespace Raww;


class Path {
    public static $base_url  = '/';
    protected static $_paths = array();

    public static function register($context, $folder){
        if(!isset(self::$_paths[$context])) {
            self::$_paths[$context] = array();
        }
        array_unshift(self::$_paths[$context], rtrim(str_replace(DIRECTORY_SEPARATOR,'/',$folder), '/').'/');
    }

    public static function get($file){
        $parts = explode(':', $file, 2);

        if(count($parts)==2){
           if(!isset(self::$_paths[$parts[0]])) return false;

           foreach(self::$_paths[$parts[0]] as &$path){
               if(file_exists($path.$parts[1])){
                  return $path.$parts[1];
               }
           }
        }
        
        return false;
    }
}
