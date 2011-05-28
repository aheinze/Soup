<?php

namespace Raww\Assets;


class Manager extends \Raww\AppContainer {
  
  protected $assets = array();
  protected $references = array();
  protected $dumped_references = array();

  /**
  * ...
  *
  */ 
  public function register($name,$options){
    
    $this->assets[$name] = $options;
    
  }


  /**
  * ...
  *
  */ 
  public function addReference($name,$options){
    $this->references[$name] = $options;
  }

  /**
  * ...
  *
  */ 
  public function dump($name, $type="js", $cache_time = 600){

    if(!isset($this->assets[$name])) return;

    $cache_key = "asset_".$name."_".$type;

    if($cached = $this->app['cache']->read($cache_key)) {
      return $cached;
    }


    $output    = array();

    foreach ($this->assets[$name] as $asset) {

      //handle references
      if(substr($asset['file'], 0,4)=="ref:"){
       
       list($prefix, $ref_name) = explode(":", $asset['file']);
       
       if(!isset($this->references[$ref_name]) || isset($this->dumped_references[$ref_name])) continue;

       $this->dumped_references[$ref_name] = true;
       
       $asset = $this->references[$ref_name];

      }

      $file    = $asset['file'];
      $ext     = strtolower(array_pop(explode(".", $file)));
      $content = '';

      if (strpos($file, ':') !== false && $____file = $this->app['path']->get($file)) {
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
      'gzip' => true,
      'mime' => $type,
    ));

    if($cache_time) {
      $this->app['cache']->write($cache_key, $response, $cache_time);
    }
    
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
      if(!preg_match("#^(http|/|data\:)#",trim($imgpath))){
        $content = str_replace('url('.$imgpath.')','url('.$csspath.str_replace('"','',$imgpath).')',$content);
      }
    }

    return $content;
  }

}