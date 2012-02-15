<?php

namespace Soup;

/**
 * Set class. Array helper class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Set implements \Iterator {
	
	protected $data = array();
	private $_position = 0;

	public function __construct($data = array()) {
		$this->data = $data;
	}

	/**
	* ...
	*
	*/ 
	public static function extract($path, $array, $callback=null){

		if(!count($array)) return $array;

		$result = array();
		
		$check = function($value) use($callback) {
			return is_callable($callback) ? call_user_func($callback,$value) : true;
		};

		$path = explode(".", $path);
		$dimension = count($path);

		switch($dimension){
		  case 0:
			return $array;
			break;
		  
		  case 1;
			foreach($array as $a){
			  if(isset($a[$path[0]]) && $check($a[$path[0]])){
				$result[] = $a[$path[0]];
			  }
			}
			break;
		  
		  case 2;
			foreach($array as $a){
			  if(isset($a[$path[0]][$path[1]]) && $check($a[$path[0]][$path[1]])){
				$result[] = $a[$path[0]][$path[1]];
			  }
			}
			break;
		  
		  case 3;
			foreach($array as $a){
			  if(isset($a[$path[0]][$path[1]][$path[2]]) && $check($a[$path[0]][$path[1]][$path[2]])){
				$result[] = $a[$path[0]][$path[1]][$path[2]];
			  }
			}
			break;
		  
		  case 4;
			foreach($array as $a){
			  if(isset($a[$path[0]][$path[1]][$path[2]][$path[3]]) && $check($a[$path[0]][$path[1]][$path[2]][$path[3]])){
				$result[] = $a[$path[0]][$path[1]][$path[2]][$path[3]];
			  }
			}
			break;
		  case 5;
			foreach($array as $a){
			  if(isset($a[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]]) && $check($a[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]])){
				$result[] = $a[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]];
			  }
			}
			break;
		}

		return $result;

	}

	public static function countDim($array = null, $all = false, $count = 0) {
		
		if ($all) {
			$depth = array($count);
			if (is_array($array) && reset($array) !== false) {
				foreach ($array as $value) {
					$depth[] = self::countDim($value, true, $count + 1);
				}
			}
			$return = max($depth);
		} else {
			if (is_array(reset($array))) {
				$return = self::countDim(reset($array)) + 1;
			} else {
				$return = 1;
			}
		}
		
		return $return;
	}

	public static function get($array, $key, $default=false){
		return isset($array[$key]) ? $array[$key] : $default;
	}

	/**
	 * Flattens an array for sorting
	 *
	 * @param array $results
	 * @param string $key
	 * @return array
	 */
	protected static function _flatten($results, $key = null) {
		$stack = array();
		foreach ($results as $k => $r) {
			$id = $k;
			if (!is_null($key)) {
				$id = $key;
			}
			if (is_array($r) && !empty($r)) {
				$stack = array_merge($stack, self::_flatten($r, $id));
			} else {
				$stack[] = array('id' => $id, 'value' => $r);
			}
		}
		return $stack;
	}

	/**
	 * Sorts an array by any value, determined by a Set-compatible path
	 *
	 * @param array $data An array of data to sort
	 * @param string $path A Set-compatible path to the array value
	 * @param string $dir Direction of sorting - either ascending (ASC), or descending (DESC)
	 * @return array Sorted array of data
	 * @link http://book.cakephp.org/2.0/en/core-utility-libraries/set.html#Set::sort
	 */
	public static function sort($data, $path, $dir="asc") {
		$originalKeys = array_keys($data);
		if (is_numeric(implode('', $originalKeys))) {
			$data = array_values($data);
		}
		$result = self::_flatten(self::extract($path, $data));
		list($keys, $values) = array(self::extract('id', $result), self::extract('value', $result));

		$dir = strtolower($dir);
		if ($dir === 'asc') {
			$dir = SORT_ASC;
		} elseif ($dir === 'desc') {
			$dir = SORT_DESC;
		}
		array_multisort($values, $dir, $keys, $dir);
		$sorted = array();
		$keys = array_unique($keys);

		foreach ($keys as $k) {
			$sorted[] = $data[$k];
		}
		return $sorted;
	}

	public function data() {
		return $this->data;
	}

	public function field($path) {
		
		return new self(Set::extract($path, $this->data));
	}

	public function where($callback) {
		
		$data = array();

		if(is_callable($callback)) {
			foreach ($this->data as $index => $item) {
				if($callback($item, $index)){
					$data[] = $item;
				}
			}
		}

		return new self($data);
	}

	public function order($path, $dir="asc") {
		
		return new self(Set::sort($this->data, $path, $dir));
	}

	// Iterator implementation
	function rewind() {
        $this->_position = 0;
    }

    function current() {
        return $this->data[$this->_position];
    }

    function key() {
        return $this->_position;
    }

    function next() {
        ++$this->_position;
    }

    function valid() {
        return isset($this->data[$this->_position]);
    }

}