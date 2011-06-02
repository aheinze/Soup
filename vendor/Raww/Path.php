<?php

namespace Raww;

/**
 * Path class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Path extends AppContainer {

    public $base_url  = '/';
    protected $_paths = array();

    public function register($context, $folder){
        if(!isset($this->_paths[$context])) {
            $this->_paths[$context] = array();
        }
        array_unshift($this->_paths[$context], rtrim(str_replace(DIRECTORY_SEPARATOR,'/',$folder), '/').'/');
    }

    public function get($file){
        $parts = explode(':', $file, 2);

        if(count($parts)==2){
           if(!isset($this->_paths[$parts[0]])) return false;

           foreach($this->_paths[$parts[0]] as &$path){
               if(file_exists($path.$parts[1])){
                  return $path.$parts[1];
               }
           }
        }
        
        return false;
    }
}
