<?php

namespace Soup;

require_once(__DIR__.'/DI.php');
require_once(__DIR__.'/AppContainer.php');
require_once(__DIR__.'/Path.php');
require_once(__DIR__.'/Autoloader.php');

/**
 * App class. Base class for a Soup app.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class App extends DI{
	
	protected $name;
	protected static $_apps = array();
	
	public $autoSearchPaths = array();
	
	/**
	 * Creates a new Soup app instance
	 *
	 * @param	string $name	Name of the app
	 * @return	void
	 */
	public function __construct($name){
		
		parent::__construct();
		
		$this->name = $name;
	}

	/**
	 * Returns the app name
	 *
	 * @return	string
	 */
	public function name() {
		return $this->name;
	}
	
	/**
	 * Creates a new Soup app instance
	 *
	 * @param	string $name	Name of an app
	 * @return	\Soup\App instance
	 */
	public static function app($name) {
		return self::$_apps[$name];
	}

	/**
	 * Get object from classes extending the AppContainer
	 *
	 * @param	string $class	Classname
	 * @return	Object
	 */
	public function pickAsHost($class) {
		return new $class($this);
	}

	/**
	 * Load file
	 *
	 * @param	string $path	Path
	 * @param	bool $once	Include once
	 * @return	Mixed
	 */
	public function load($path, $once=true) {
		if($file = $this["path"]->get($path)){
			
			$app = $this;
			
			return $once ? include_once($file):include($file);
		}

		return false;
	}
	
	/**
	 * Initialize a new Soup app instance
	 *
	 * @param	string $appname	Name of an app
	 * @param	string $config	App configuration
	 * @return	\Soup\App instance
	 */
	public static function init($appname, $config) {

		$app = new App($appname);

		$config = array_merge(array(
			'debug' 	=> true,
			'charset'  	=> 'UTF-8',
			'key'       => 'xxxxxAppKeyxxxxx',
			'language' 	=> 'en',
			'autoload' 	=> array(),
			'paths' 	=> array()
		), $config);


		if(!isset($config['base_url_path'])) {
			$config['base_url_path'] = implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
		}
		
		if(!isset($config['base_route_path'])) {
			$config['base_route_path'] = implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
		}

		foreach ($config as $key => $value) {
			$app[$key] = $value;
		}
		
		$app["base_url_path"]   = rtrim($config["base_url_path"], '/');
		$app["base_route_path"] = rtrim($config["base_route_path"], '/');
		
		$app["path"]     = new Path($app);
		$app->share("autoloader", 	function($app){ return new Autoloader($app); });
		$app->share("bench", 		function($app){ return new Bench(); });
		$app->share("session", 		function($app){ return new Session\Php($app); });
		$app->share("registry", 	function($app){ return new Registry($app); });
		$app->share("router", 		function($app){ return new Router($app); });
		$app->share("event", 		function($app){ return new Event($app); });
		$app->share("view", 		function($app){ return new View($app); });
		$app->share("i18n", 		function($app){ return new I18n($app); });
		$app->share("assets", 		function($app){ return new Assets($app); });
		$app->share("cache", 		function($app){ return extension_loaded('apc') ? new Cache\Apc($app):new Cache\File($app); });
		$app->share("request", 		function($app){ return new Request();});

		$app["path"]->register("Soup", __DIR__);
		$app["path"]->register("views", __DIR__.'/views');
		$app["path"]->register("vendor", __DIR__.'/vendor');
		
		foreach($app['paths'] as $name => $path){
			$app["path"]->register($name, $path);
		}

		// config autoload
		if(count($app["autoload"])){
			foreach($app['autoload'] as $key=>$value){
				$app['autoloader']->{$key}($value);
			}
		}

		$app['autoloader']->map(array(
			"Soup\Assets" 	=> __DIR__.'/Assets.php',
			"Soup\Bench" 	=> __DIR__.'/Bench.php',
			"Soup\I18n" 	=> __DIR__.'/I18n.php',
			"Soup\Router" 	=> __DIR__.'/Router.php',
			"Soup\Event" 	=> __DIR__.'/Event.php',
			"Soup\Controller" => __DIR__.'/Controller.php',
			"Soup\Response" => __DIR__.'/Response.php',
			"Soup\View" 	=> __DIR__.'/View.php',
			"Soup\Request" 	=> __DIR__.'/Request.php',
		));

		$app['autoloader']->namespaces(array("Soup"=>__DIR__));

		//register app autoloader
		spl_autoload_register(function($resource) use($app){

			$app["autoloader"]->load($resource);
		});

		register_shutdown_function(function() use($app) {

			$error = error_get_last();
			
			if ($error && in_array($error['type'], array(E_ERROR,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR))){
				
				$app["event"]->trigger("error", array("error"=>$error));
				
				if(!headers_sent()){
				
					ob_end_clean();
					
					if($app['debug']){						
						
						//is ajax
						if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))){
							
							$response = new Response(null, array(
								"body" => json_encode(array("error"=>$error)),
								"status" => "500"
							));	

						}else{

							$response = new Response(null, array(
								"body" => $app["view"]->render("views:error/error.php", array("error"=>$error)),
								"status" => "500"
							));	
						}
						
					}else{
					
						$response = new Response(null, array(
							"body" => $app["view"]->render("views:error/404.php", array("message"=>"Ooooops!")),
							"status" => "500"
						));
					}
					
					$response->send();
				}

				return;
			}

			$app["event"]->trigger("shutdown");
		});

		//error_reporting($app['debug'] ? E_ALL : 0);
		
		self::$_apps[$appname] = $app;
		
		return self::$_apps[$appname];
	}
	
	/**
	 * Initialize a new Soup app instance
	 *
	 * @param	string $route	Route
	 * @return	void
	 */
	public function handle($route, $raw=false) {
		
		$this["route"] = $route;
		
		if(isset($app["timezone"])) {
			date_default_timezone_set($app["timezone"]);
		}


		// register /tests route in debug mode
		if($this["debug"]){
			
			$app = $this;

			if((isset($this["profiler"]) && $this["profiler"]) || isset($_GET["soup-profile"])) {
				$profiler = new \Soup\Bench();
				$profiler->start("soup.profiler");
			}

			$this["router"]->bind("#^/--soup#", function() use($app){
				$controller = new \Soup\Controller\Soup($app);
				return $controller->route();
			});
		}

		$this["event"]->trigger("before", array($this));

		$response = $this["router"]->dispatch($this["route"]);
		
		if($response && is_object($response) && method_exists($response, 'send')) {
			
			$this["event"]->trigger("before_send", array($response));

		} else {

			$response = new Response(null, array(
				"body" => $this["view"]->render("views:error/404.php", array("message"=>$this["route"])),
				"status" => "404"
			));
			
			$this["event"]->trigger("error_404", array("path"=>$this["route"], "response" => $response));
			
		}

		if(!$raw){
			$response->send();
		}

		$this["event"]->trigger("after", array($this));

		if($raw){
			return $response->body;
		}

		if(isset($profiler)){
			$profiler->stop("soup.profiler");

			$data = $this["cache"]->read("soup.profiler", array());

			if(!isset($data[$this["route"]])){
				$data[$this["route"]] = array();
			}

			$info                   = $profiler->get("soup.profiler");
			$data[$this["route"]][] = $info;

			$this["cache"]->write("soup.profiler", $data);

		}
	}

	public function debug() {
	
		$vars   = func_get_args();           
		$_from  = debug_backtrace();
		$output = array();
		
		$output[] = '<div class="soup-debug">';
		  $output[] = '<strong title="'.$_from[0]['file'].'">' . $_from[0]['file'] . '</strong>';
		  $output[] = ' (line: <strong>' . $_from[0]['line'] . '</strong>)';
		  
		  foreach ($vars as $var){
			  $output[] = '<pre>';
			  $output[] = print_r($var, true);
			  $output[] = "</pre>";
		  }

		$output[] = '<div>';

		echo implode("\n",$output);
	}

	public function crypt($str) {
		return Sec::rc4($str, $this["key"], true);
	}

	public function hash($str) {
		return md5($this->crypt($str));
	}
}

// helper functions

function sluggify($string, $replacement = '-') {
		$quotedReplacement = preg_quote($replacement, '/');

		$merge = array(
			'/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
			'/\\s+/' => $replacement,
			sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
		);

		$map = array(
				'/ä|æ|ǽ/' => 'ae',
				'/ö|œ/' => 'oe',
				'/ü/' => 'ue',
				'/Ä/' => 'Ae',
				'/Ü/' => 'Ue',
				'/Ö/' => 'Oe',
				'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
				'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
				'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
				'/ç|ć|ĉ|ċ|č/' => 'c',
				'/Ð|Ď|Đ/' => 'D',
				'/ð|ď|đ/' => 'd',
				'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
				'/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
				'/Ĝ|Ğ|Ġ|Ģ/' => 'G',
				'/ĝ|ğ|ġ|ģ/' => 'g',
				'/Ĥ|Ħ/' => 'H',
				'/ĥ|ħ/' => 'h',
				'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
				'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
				'/Ĵ/' => 'J',
				'/ĵ/' => 'j',
				'/Ķ/' => 'K',
				'/ķ/' => 'k',
				'/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
				'/ĺ|ļ|ľ|ŀ|ł/' => 'l',
				'/Ñ|Ń|Ņ|Ň/' => 'N',
				'/ñ|ń|ņ|ň|ŉ/' => 'n',
				'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
				'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
				'/Ŕ|Ŗ|Ř/' => 'R',
				'/ŕ|ŗ|ř/' => 'r',
				'/Ś|Ŝ|Ş|Š/' => 'S',
				'/ś|ŝ|ş|š|ſ/' => 's',
				'/Ţ|Ť|Ŧ/' => 'T',
				'/ţ|ť|ŧ/' => 't',
				'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
				'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
				'/Ý|Ÿ|Ŷ/' => 'Y',
				'/ý|ÿ|ŷ/' => 'y',
				'/Ŵ/' => 'W',
				'/ŵ/' => 'w',
				'/Ź|Ż|Ž/' => 'Z',
				'/ź|ż|ž/' => 'z',
				'/Æ|Ǽ/' => 'AE',
				'/ß/' => 'ss',
				'/Ĳ/' => 'IJ',
				'/ĳ/' => 'ij',
				'/Œ/' => 'OE',
				'/ƒ/' => 'f'
		) + $merge;

		return preg_replace(array_keys($map), array_values($map), $string);
}

function stripslashes_deep($value) {
	if ( is_array($value) ) {
		$value = array_map('\Soup\stripslashes_deep', $value);
	} elseif ( is_object($value) ) {
		$vars = get_object_vars( $value );
		foreach ($vars as $key=>$data) {
			$value->{$key} = stripslashes_deep( $data );
		}
	} else {
		$value = stripslashes($value);
	}

	return $value;
}

function fetch_from_array(&$array, $index=null, $default = null) {
	
	if (is_null($index)) {
		
		return $array;
		
	} elseif(isset($array[$index])) {
		
		return $array[$index];
		
	} elseif(strpos($index, '/')){
			
		$keys = explode('/', $index);
		
		switch(count($keys)){
		  
		  case 1:
			if(isset($array[$keys[0]])){
			  return $array[$keys[0]];
			}
			break;
		  
		  case 2:
			if(isset($array[$keys[0]][$keys[1]])){  
			  return $array[$keys[0]][$keys[1]];
			}
			break;
		  
		  case 3:
			if(isset($array[$keys[0]][$keys[1]][$keys[2]])){
			  return $array[$keys[0]][$keys[1]][$keys[2]];
			}
			break;
			
		  case 4:
			if(isset($array[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
			  return $array[$keys[0]][$keys[1]][$keys[2]][$keys[3]];
			}
			break;
		}
	}

	return $default;
}