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

      if (strpos($file, ':') !== false && $____file = \Raww\Path::get($file)) {
         $file = $____file;
      }

      if($ext!=$type) continue;

      switch ($ext) {
        
        case 'js':
          
          $content = file_get_contents($file);

          break;

        case 'css':
          
          $content = self::rewriteCssUrls(file_get_contents($file), dirname($file));

          break;
        
        default:
          continue;
      }

      $output[$type][] = $content;
    }

    $response = new \Raww\Response(null,array(
      'body' => implode("",$output[$type]),
      //'gzip' => true,
      //'mime' => $type,
    ));

    return $response;
  }

  protected static function rewriteCssUrls($content, $source_dir){

    preg_match_all('/url\((.*)\)/',$content,$matches);

    $root_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $csspath  = "";

    if (strlen($root_dir) < strlen($source_dir)) {
      $csspath = trim(str_replace($root_dir, '', $source_dir), "/")."/";
    } else {
      # todo
    }

    foreach($matches[1] as $imgpath){
      if(!preg_match("#^(http|/)#",trim($imgpath))){
        $content = str_replace('url('.$imgpath.')','url('.$csspath.str_replace('"','',$imgpath).')',$content);
      }
    }

    return $content;
  }

}