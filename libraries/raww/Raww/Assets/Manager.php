<?php

namespace Raww\Assets;


class Manager {
  
  protected static $assets = array();
  protected static $references = array();
  protected static $dumped_references = array();

  /**
  * ...
  *
  */ 
  public static function register($name,$options){
    
    self::$assets[$name] = $options;
    
  }


  /**
  * ...
  *
  */ 
  public static function addReference($name,$options){
    
    self::$references[$name] = $options;
    
  }

  /**
  * ...
  *
  */ 
  public static function dump($name, $type="js"){
    
    $output = array();

    foreach (self::$assets[$name] as $asset) {

      //handle references
      if(substr($asset['file'], 0,4)=="ref:"){
       
       list($prefix, $ref_name) = explode(":", $asset['file']);
       
       if(!isset(self::$references[$ref_name]) || isset(self::$dumped_references[$ref_name])) continue;

       self::$dumped_references[$ref_name] = true;
       
       $asset = self::$references[$ref_name];
      }

      $file    = $asset['file'];
      $ext     = strtolower(array_pop(explode(".", $file)));
      $content = '';

      if($ext!=$type) continue;

      switch ($ext) {
        
        case 'js':
          
          if (strpos($file, ':') !== false && $____file = \Raww\Path::get($file)) {
            $file = $____file;
          }
          
          $content = file_get_contents($file);

          break;

        case 'css':
          # code...
          break;
        
        default:
          continue;
      }

      $output[$type][] = $content;
    }

    $response = new \Raww\Response(null,array(
      'body' => implode("",$output[$type]),
      'gzip' => true,
      'mime' => $type,
    ));

    return $response;
  }

}