<?php

namespace Soup;

/**
 * Autoloader class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Autoloader extends \Soup\AppContainer {

	protected $namespaces  = array();
	protected $aliases     = array();
	protected $mappings    = array();
	protected $directories = array();


	public function addIncludePath($path){
	    
		if(is_array($path)){
			foreach($path as $p){
				set_include_path(get_include_path() . PATH_SEPARATOR . $p);
			}
		}else{
			set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		}
	}

	public function load($resource) {

			if (isset($this->aliases[$resource])){
				
				class_alias($this->aliases[$resource], $resource);
				return;
			} 

			if (isset($this->mappings[$resource])) {
				
				require ($this->mappings[$resource]);
				return;
			}

			foreach ($this->namespaces as $namespace => $dir) {
				if (strpos($resource, $namespace)===0) {
					
					$class = substr($resource, strlen($namespace));
					$path  = $dir.ltrim(str_replace('\\', '/', $class).'.php','/');
					
					if(file_exists($path)) {
						require($path);
						return;
					}
				}
			}

			foreach($this->directories as $dir){
				
				$path = $dir.ltrim(str_replace('\\', '/', $resource).'.php','/');

				if(file_exists($path)) {
					require($path);
					return;
				}
			}
	}

	public function namespaces($mappings) {
		
		$namespaces = array();

		foreach ($mappings as $namespace => $directory) {

			$namespace = trim($namespace, '\\').'\\';

			unset($this->namespaces[$namespace]);

			$namespaces[$namespace] = rtrim($directory, '/').'/';
		}

		$this->namespaces = array_merge($namespaces, $this->namespaces);
	}

	public function map($mappings) {
		$this->mappings = array_merge($this->mappings, $mappings);
	}

	public function alias($class, $alias) {
		$this->aliases[$alias] = $class;
	}

	public function directories($dirs){
		
		$dirs = (array) $dirs;

		foreach ($dirs as &$dir) {
			$dir = rtrim($dir, '/').'/';
		}

		$this->directories = array_unique(array_merge($this->directories, $dirs));
	}
}