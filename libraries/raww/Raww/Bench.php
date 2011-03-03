<?php

namespace Raww;

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
			self::$m[$name]['memory_usage'] = self::$m[$name]['memory_stop'] - self::$m[$name]['memory_start'];
            self::$m[$name]['duration'] = self::$m[$name]['stop'] - self::$m[$name]['start'];
      
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
}
