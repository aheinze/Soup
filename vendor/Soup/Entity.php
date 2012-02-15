<?php

namespace Soup;

/**
 * Entity class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Entity {

  protected $_validate_errors;


  public function __construct($data = array()) {
    
    foreach ($data as $key => $value) {
      $this->{$key} = $value;
    }
    
  }

  /**
  * ...
  *
  */ 
  public function validates(){
    
    $properties  = get_object_vars($this);
    $annotations = Annotations::getClass(get_class($this));
    
    $this->_validate_errors = array();

    foreach ($properties as $property => $value) {
      
      if(substr($property, 0, 1)==="_") continue;

      if(isset($annotations['properties'][$property]['allowEmpty']) && empty($value)){
        continue;
      }

      if(isset($annotations['properties'][$property]['check'])){
        foreach($annotations['properties'][$property]['check'] as $check){
          if(!Validate::$check['validate']($value)) {
            $this->_validate_errors[$property] = $check['message'];
          }
        }
      }
    }

    return count($this->_validate_errors) ? false : true;
  }

  /**
  * ...
  *
  */ 
  public function getValidationErrors() {
    return $this->_validate_errors;
  }


  /**
  * ...
  *
  */ 
  public function toArray() {
    
    $array = array();

    foreach (get_object_vars($this) as $property => $value) {
      if(substr($property, 0, 1)!=="_" && !is_null($value)) $array[$property] = $value;
    }

    return $array;
  }
  
}