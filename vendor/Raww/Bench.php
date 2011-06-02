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
 
	protected $m = array();

	/**
	 * Starts benchmark
	 *
	 * @param	string $name	benchmark group name
	 * @return	void
	 */
	public function start($name){
		$this->m[$name] = array(
			'start'        => microtime(true),
			'stop'         => false,
            'duration'     => null,
			'memory_start' => memory_get_usage(),
			'memory_stop'  => false
		);
	}

	/**
	 * Stops benchmark
	 *
	 * @param	string $name	benchmark group name
	 * @return	mixed
	 */
	public function stop($name){
		if(isset($this->m[$name]) && $this->m[$name]['stop'] === false){
			$this->m[$name]['stop'] = microtime(true);
			$this->m[$name]['memory_stop'] = memory_get_usage();
			$this->m[$name]['memory_usage'] = $this->formatSize($this->m[$name]['memory_stop'] - $this->m[$name]['memory_start']);
            $this->m[$name]['duration'] = $this->formatTime($this->m[$name]['stop'] - $this->m[$name]['start']);
      
            return $this->m[$name];
		}
	}

	/**
	 * Returns benchmark information
	 *
	 * @param	string $name	benchmark group name
	 * @return	mixed
	 */ 
	public function get($name=false){
		if ($name === false){
			return $this->m;
		}

		if(!isset($this->m[$name])){
			return false;
    }

		if($this->m[$name]['stop'] === false){
			$this->stop($name);
		}

		return $this->m[$name];
	}
    
    protected function formatSize($size) {
      $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
      return ($size == 0) ? "n/a" : (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]); 
    }
	
	protected function formatTime($time) {
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
