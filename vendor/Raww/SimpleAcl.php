<?php

namespace Raww;

/**
 * SimpleAcl class. Simple acl implementation.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class SimpleAcl {

  /**
  * ...
  *
  */
  protected $resources = array();
  protected $roles     = array();
  protected $rights    = array();
  
  /**
  * ...
  *
  */ 
  public function addResource($resource, $actions = array()){
    $this->resources[$resource] = $actions;
  }
  
  /**
  * ...
  *
  */ 
  public function addRole($role, $isSuperAdmin = false){
    $this->roles[$role] = $isSuperAdmin;
  }
  
  /**
  * ...
  *
  */ 
  public function allow($role, $resource, $actions = array()){
    
    if(isset($this->resources[$resource]) && isset($this->roles[$role])){
        
        $actions = (array)$actions;
        
        if(!count($actions)){
            $actions = $this->resources[$resource];
        }
        
        foreach($actions as &$action){
            if(in_array($action, $this->resources[$resource])){
                $this->rights[$role][$resource][$action] = true;
            }
        }
    }
  }
  
  /**
  * ...
  *
  */ 
  public function deny($role, $resource, $actions = array()){
    
    if(isset($this->resources[$resource]) && isset($this->roles[$role])){
        
        $actions = (array)$actions;
        
        if(!count($actions)){
            $actions = $this->resources[$resource];
        }
        
        foreach($actions as &$action){
            if(isset($this->rights[$role][$resource][$action])){
                unset($this->rights[$role][$resource][$action]);
            }
        }
    }
  }

  /**
  * ...
  *
  */ 
  public function isAllowed($role, $resource, $action){
    
    if(!isset($this->resources[$resource])){
        return false;
    }
    
    if(is_array($role)){
        foreach($role as $r){
            
            if(!isset($this->roles[$r])) continue;
            
            if($this->roles[$r]==true || isset($this->rights[$r][$resource][$action])) {
                return true;
            }
        }
    }else{
        
        if(!isset($this->roles[$role])) return false;
        
        if($this->roles[$role]==true || isset($this->rights[$role][$resource][$action])) {
            return true;
        }
    }

    return false;
  }
  
}