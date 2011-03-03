<?php

namespace Raww;

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
  public static function isEmail($value){
   
		return preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string) $value);
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