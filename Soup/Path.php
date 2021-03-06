<?php

namespace Soup;

/**
 * Path class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Path extends AppContainer {

    protected $_paths = array();

	/**
	 * Register a path for a context
	 *
	 * @param	string $context	Alias for the folder
	 * @param	string $folder	Path to folder
	 * @return	void
	 */
    public function register($context, $folder){
        if(!isset($this->_paths[$context])) {
            $this->_paths[$context] = array();
        }
        array_unshift($this->_paths[$context], rtrim(str_replace(DIRECTORY_SEPARATOR,'/',$folder), '/').'/');
    }

	/**
	 * Returns resolved path
	 *
	 * @param	string $file	
	 * @return	void
	 */
    public function get($file){
        $parts = explode(':', $file, 2);

        if(count($parts)==2){
           if(!isset($this->_paths[$parts[0]])) return false;

           foreach($this->_paths[$parts[0]] as &$path){
               if(file_exists($path.$parts[1])){
                  return $path.$parts[1];
               }
           }
        }else{
           if(file_exists($file)){
              return $file;
           }
        }
        
        return false;
    }


    public function req($file){
        require($this->get($file));
    }
    public function req_once($file){
        require_once($this->get($file));
    }
    public function inc($file){
        include($this->get($file));
    }
    public function inc_once($file){
        include_once($this->get($file));
    }
}
