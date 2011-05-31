<?php

namespace Raww;


class Assets extends \Raww\AppContainer {
  
  public static $filters = array();
  
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
          
          $content = self::rewriteCssUrls(file_get_contents($file), dirname($file), $this->app['base_url']);

          break;
        
        default:
          continue;
      }

      $output[$type][] = $content;
    }

    $response = $this->app["response"]->assign(array(
      'body' => implode("",$output[$type]),
      'gzip' => true,
      'mime' => $type,
    ));

    if($cache_time) {
      $this->app['cache']->write($cache_key, $response, $cache_time);
    }
    
    return $response;
  }

  protected static function rewriteCssUrls($content, $source_dir, $base_path){

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
        $content = str_replace('url('.$imgpath.')','url('.$base_path.'/'.$csspath.str_replace('"','',$imgpath).')',$content);
      }
    }

    return $content;
  }

}

// Filters

Assets::$filters["minify_css"] = function(&$str) {
		
		// Colons cannot be globally matched safely because of pseudo-selectors etc.
		$innerbrace = function($match) {
			return preg_replace('#\s*:\s*#', ':', $match[0]);
		};
		
		$str = preg_replace_callback( '#\{[^}]+\}#s', $innerbrace, trim($str));

		$replacements = array(
			'#\s{2,}#'                          => ' ',      // Remove double spaces
			'#\s*(;|,|\{)\s*#'                  => '$1',     // Clean-up around delimiters
			'#\s*;*\s*\}\s*#'                   => '}',      // Clean-up closing braces
			'#[^}{]+\{\s*}#'                    => '',       // Strip empty statements
			'#([^0-9])0[a-zA-Z%]{2}#'           => '${1}0',  // Strip unnecessary units on zeros
			'#:(0 0|0 0 0|0 0 0 0)([;}])#'      => ':0${2}', // Collapse zero lists
			'#(background-position):0([;}])#'   => '$1:0 0$2', // Restore any overshoot
			'#([^\d])0(\.\d+)#'                 => '$1$2',   // Strip leading zeros on floats
			'#(\[)\s*|\s*(\])|(\()\s*|\s*(\))#' => '${1}${2}${3}${4}',  // Clean-up bracket internal space
			'#\s*([>~+=])\s*#'                  => '$1',     // Clean-up around combinators
			'#\#([0-9a-f])\1([0-9a-f])\2([0-9a-f])\3#i'
			                                    => '#$1$2$3', // Reduce Hex codes
		);

		$str = preg_replace(array_keys($replacements), array_values($replacements), $str);
};