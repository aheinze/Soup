<?php

namespace Raww;


class Entity {

  protected $_validate_errors;

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
  
}