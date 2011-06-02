<?php

namespace Raww;

/**
 * Validate class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Validate {
  /**
  * ...
  *
  */ 
  public static function isAlphaNumeric($value){
    return preg_match("^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]+$/mu",$value);
  } 
  /**
  * ...
  *
  */ 
  public static function isNotEmpty($value){
    return preg_match("/[^\s]+/m",$value);
  }
  /**
  * ...
  *
  */ 
  public static function isBlank($value){
    return preg_match("/[^\\s]/",$value);
  }
  /**
  * ...
  *
  */ 
  public static function isUrl($value){
   
		return empty($value) || filter_var($value, FILTER_VALIDATE_URL);
  }
  /**
  * ...
  *
  */ 
  public static function isValidIp($value){
   
		return empty($value) || filter_var($value, FILTER_VALIDATE_IP);
  }
  /**
  * ...
  *
  */ 
  public static function isEmail($value){
   
		return filter_var($value, FILTER_VALIDATE_EMAIL);
  }
  /**
  * ...
  *
  */ 
  public static function isBoolean($value) {
		return in_array($value, array(0, 1, '0', '1', true, false), true);
	}
  /**
  * ...
  *
  */ 
  public static function isNumeric($value) {
    return is_numeric($value);
  }
  /**
  * ...
  *
  */ 
  public static function isFloat($value) {
    return is_float($value);
  }
  /**
  * ...
  *
  */ 
  public static function isInt($value) {
    return is_int($value);
  }
  /**
  * ...
  *
  */ 
  public static function hasMinLength($value, $min) {
		return (strlen($value) >= $min);
	}
  /**
  * ...
  *
  */ 
  public static function hasMaxLength($value, $max) {
		return (strlen($value) <= $max);
	}
  /**
  * ...
  *
  */ 
	public static function isInRange($value, $lower = null, $upper = null) {
		if (!is_numeric($value)) {
			return false;
		}
		if (isset($lower) && isset($upper)) {
			return ($value > $lower && $value < $upper);
		}
		return is_finite($value);
	}
  
}