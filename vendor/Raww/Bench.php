<?php

namespace Raww;

/**
 * Bench class. Measure and benchmark class
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Bench {
 
	private static $m = array();

  /**
  * ...
  *
  */ 
	public static function start($name){
		self::$m[$name] = array(
			'start'        => microtime(true),
			'stop'         => false,
            'duration'     => null,
			'memory_start' => memory_get_usage(),
			'memory_stop'  => false
		);
	}

  /**
  * ...
  *
  */ 
	public static function stop($name){
		if(isset(self::$m[$name]) && self::$m[$name]['stop'] === false){
			self::$m[$name]['stop'] = microtime(true);
			self::$m[$name]['memory_stop'] = memory_get_usage();
			self::$m[$name]['memory_usage'] = self::formatSize(self::$m[$name]['memory_stop'] - self::$m[$name]['memory_start']);
            self::$m[$name]['duration'] = self::formatTime(self::$m[$name]['stop'] - self::$m[$name]['start']);
      
            return self::$m[$name];
		}
	}

  /**
  * ...
  *
  */ 
	public static function get($name=false){
		if ($name === false){
			return self::$m;
		}

		if(!isset(self::$m[$name])){
			return false;
    }

		if(self::$m[$name]['stop'] === false){
			self::stop($name);
		}

		return self::$m[$name];
	}
    
    protected static function formatSize($size) {
      $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      return ($size == 0) ? "n/a" : (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); 
    }
	
	protected static function formatTime($time) {
		$ret = $time;
		$formatter = 0;
		$formats = array('ms', 's', 'm');
		if($time >= 1000 && $time < 60000) {
			$formatter = 1;
			$ret = ($time / 1000);
		}
		if($time >= 60000) {
			$formatter = 2;
			$ret = ($time / 1000) / 60;
		}
		$ret = number_format($ret,3,'.','') . ' ' . $formats[$formatter];
		return $ret;
	}
}
