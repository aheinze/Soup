<?php

namespace Raww;

/**
 * Assets class. Combine, minify and apply filters to js and css files.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Assets extends \Raww\AppContainer {
  
	/**
	 * @var filters	Collection of asset filters
	 */
	public static $filters = array();

	protected $assets = array();
	protected $references = array();
	protected $dumped_references = array();

	/**
	 * Register an asset group
	 *
	 * @param	string $name	Name of the group to register
	 * @param	array $options	Containing files and filter options
	 * @return	void
	 */
	public function register($name,$options){

		$this->assets[$name] = $options;
	}

	/**
	 * Register an asset reference
	 *
	 * @param	string $name	Name of the reference
	 * @param	array $options	Containing file and filter options
	 * @return	void
	 */
	public function addReference($name,$options){
		$this->references[$name] = $options;
	}

	/**
	 * Dump an asset group (combine + apply filters)
	 *
	 * @param	string $name	Name of the asset group
	 * @param	string $type	(js|css) all css or js files of an asset group
	 * @param	int $cache_time	Cache lifetime (0 = no caching)
	 * @return	string
	 */
	public function dump($name, $type="js", $cache_time = 600){

		if(!isset($this->assets[$name])) return;

		$cache_key = "asset_".$name."_".$type;

		if($cache_time && $cached = $this->app['cache']->read($cache_key)) {
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

		  $asset = array_merge(array(
			"app"       => $this->app,
			"filters"   => array(),
			"ext"		=> strtolower(array_pop(explode(".", $asset['file']))),
			"base_path" => $_SERVER['SCRIPT_FILENAME'], 
			"base_url"	=> $this->app["base_url_path"]
		  ), $asset);

		  $file    = $asset['file'];
		  $ext     = $asset['ext'];
		  $content = '';

		  if (strpos($file, ':') !== false && $____file = $this->app['path']->get($file)) {
			 $asset['file'] = $file = $____file;
		  }

		  if($ext!=$type) continue;

		  switch ($ext) {
			
			case 'js':
			  
			  $content = file_get_contents($file);
			  
			  foreach($asset['filters'] as $filter){
				if(isset(self::$filters[$filter])){
					$content = call_user_func(self::$filters[$filter], $content, $asset);
				}
			  }
			  
			  break;
			
			case 'css':
			    
			  $content = file_get_contents($file);
			  
			  foreach($asset['filters'] as $filter){
				if(isset(self::$filters[$filter])){
					$content = call_user_func(self::$filters[$filter], $content, $asset);
				}
			  }
			  
			  $content = self::rewriteCssUrls($content, $asset);
			  
			  break;
			
			default:
			  continue;
		  }

		  $output[$type][] = $content;
		}

		$response = new Response(null, array(
		  'body' => implode("",$output[$type]),
		  'gzip' => true,
		  'mime' => $type,
		));

		if($cache_time) {
		  $this->app['cache']->write($cache_key, $response, $cache_time);
		}

		return $response;
	}

	/**
	 * Rewrite
	 *
	 * @param	string $content		content of css file
	 * @param	string $source_dir	dir of css file
	 * @param	int $base_path		app base path
	 * @return	string
	 */
	protected static function rewriteCssUrls($content, $asset){
		
		$base_path  = rtrim($asset['base_url'], '/');
		$source_dir = dirname($asset["file"]);
		$root_dir   = dirname($asset['base_path']);
		
		preg_match_all('/url\((.*)\)/',$content,$matches);

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

Assets::$filters["process_css"] = function($str, $asset) {
	
	if($asset["ext"]!="css") return $str;

	/*
		@constants {
			constantName: constantValue;
		}
		.selector {
			propertyName: const(constantName);
		}
	*/
	if (preg_match_all('#@constants\s*\{\s*([^\}]+)\s*\}\s*#i', $str, $matches)) {
		
		$constants = array();
		
		foreach ($matches[0] as $i => $constant) {
			$str = str_replace($constant, '', $str);
			preg_match_all('#([_a-z0-9]+)\s*:\s*([^;]+);#i', $matches[1][$i], $vars);
			foreach ($vars[1] as $var => $name) {
				$constants["const($name)"] = $vars[2][$var];
			}
		}
		
		if (count($constants)) {
			$str = str_replace(array_keys($constants), array_values($constants), $str);
		}
	}
	
	
	/*
		@base(baseName) {
			propertyName: propertyValue;
			propertyName: propertyValue;
			propertyName: propertyValue;
		}
	
		.selector {
			based-on: base(baseName);
		}
	*/
	
	if (preg_match_all('#@base\(([^\s\{]+)\)\s*\{(\s*[^\}]+)\s*\}\s*#i', $str, $matches)) {
		
		$bases = array();
		
		$replace_bases = function($bases, $css, $current_base_name = false){
			// As long as there's based-on properties in the CSS string
			// Get all instances
			while (preg_match_all('#\s*based-on:\s*base\(([^;]+)\);#i', $css, $matches)) {
				// Loop through based-on instances
				foreach ($matches[0] as $key => $based_on) {
					$styles = '';
					$base_names = array();
					// Determine bases
					$base_names = preg_split('/[\s,]+/', $matches[1][$key]);
					// Loop through bases
					foreach ($base_names as $base_name) {
						// Looks like a circular reference, skip to next base
						if ($current_base_name && $base_name == $current_base_name) {
							$styles .= '/* RECURSION */';
							continue;
						}
						$styles .= $bases[$base_name];
					}

					// Insert styles this is based on
					$css = str_replace($based_on, $styles, $css);
				}
			}
			return $css;
		};
		
		// For each declaration
		foreach ($matches[0] as $key => $base) {
			// Remove the @base declaration
			$str = str_replace($base, '', $str);

			// Add declaration to our array indexed by base name
			$bases[$matches[1][$key]] = $matches[2][$key];
		}

		// Parse nested based-on properties, stopping at circular references
		foreach ($bases as $base_name => $properties) {
			$bases[$base_name] = $replace_bases($bases, $properties, $base_name);
		}
		
		if(count($bases)) {
			// Now apply replaced based-on properties in our CSS
			$str = $replace_bases($bases, $str);
		}
	}
	
	return $str;
};

Assets::$filters["minify_css"] = function($str, $asset) {
		
		if($asset["ext"]!="css") return $str;

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
			//'#\#([0-9a-f])\1([0-9a-f])\2([0-9a-f])\3#i' => '#$1$2$3', // Reduce Hex codes
		);

		return preg_replace(array_keys($replacements), array_values($replacements), $str);
};


Assets::$filters["minify_js"] = function($str, $asset) {
	
	if($asset["ext"]!="js") return $str;

	if (!class_exists("JSMin")) {
		$asset["app"]["path"]->req_once("vendor:jsmin.php");
	}

	try {
		$minified = \JSMin::minify($str);
	} catch(Exception $e) {
		$minified = $str;
	}
	
	return $minified;
};