<?php

namespace Raww;

spl_autoload_register(function($resource){
	
	if(strpos($resource,__NAMESPACE__.'\\')===0) {
		
		$path = dirname(__DIR__).'/'.str_replace('\\', '/', $resource).'.php';

		if(file_exists($path)){
			require($path);
			return;
		}            
	}
});

/**
 * App class. Base class for a Raww app.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class App extends DI{
	
	protected $name;
	protected static $_apps = array();
	
	/**
	 * Creates a new Raww app instance
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
	 * Creates a new Raww app instance
	 *
	 * @param	string $name	Name of an app
	 * @return	\Raww\App instance
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
	public function pickashost($class) {
		return new $class($this);
	}
	
	/**
	 * Initialize a new Raww app instance
	 *
	 * @param	string $appname	Name of an app
	 * @param	string $config	App configuration
	 * @return	\Raww\App instance
	 */
	public static function init($appname, $config) {

		$app = new App($appname);
		
		if(!isset($config['base_url_path'])) {
			$config['base_url_path'] = implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
		}
		
		if(!isset($config['base_route_path'])) {
			$config['base_route_path'] = implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
		}
		
		$app["base_url_path"]   = rtrim($config["base_url_path"], '/');
		$app["base_route_path"] = rtrim($config["base_route_path"], '/');
		
		$app["path"]     = new Path($app);
		$app["bench"]    = $app->share(function($app){ return new Bench(); });
		$app["session"]  = $app->share(function($app){ return new Session\Php($app); });
		$app["registry"] = $app->share(function($app){ return new Registry($app); });
		$app["router"]   = $app->share(function($app){ return new Router($app); });
		$app["event"]    = $app->share(function($app){ return new Event($app); });
		$app["view"]      = $app->share(function($app){ return new Template($app); });
		$app["i18n"]     = $app->share(function($app){ return new I18n($app); });
		$app["assets"]   = $app->share(function($app){ return new Assets($app); });
		$app["cache"]    = $app->share(function($app){ return new Cache\File($app); });
		$app["request"]  = $app->share(function($app){
			return new \Raww\Request();
		});
		
		$app["response"] = function($app){ return new \Raww\Response(); };

		$app["path"]->register("views", __DIR__.'/views');
		$app["path"]->register("vendor", __DIR__.'/vendor');
		
		foreach($config['paths'] as $name => $path){
			$app["path"]->register($name, $path);
		}
		
		spl_autoload_register(function($resource) use($app){
			
			// Autoload module and lib classes
			foreach(array('modules', 'lib', 'vendor') as $loc){
				if($path = $app['path']->get("$loc:".str_replace('\\', '/', $resource).'.php')){
					require($path);
					return;
				}
			}
		});

		register_shutdown_function(function() use($app) {

			$error = error_get_last();
			
			if ($error && in_array($error['type'], array(E_ERROR,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR))){
				
				$app["event"]->trigger("error", array("error"=>$error));
				
				if(!headers_sent()){
				
					ob_end_clean();
					
					if($app['registry']->get("debug", false)){						
						
						$response = $app['response']->assign(array(
							"body" => $app["tpl"]->render("views:error/error.php", array("error"=>$error)),
							"status" => "500"
						));
						
					}else{
					
						$response = $app['response']->assign(array(
							"body" => $app["tpl"]->render("views:error/404.php", array("message"=>"ooooops!")),
							"status" => "500"
						));
					}
					
					$response->flush();
				}

				return;
			}

			$app["event"]->trigger("shutdown");
		});
		
		if($app_bootstrap = $app["path"]->get("config:bootstrap.php")){
			require($app_bootstrap);
		}

		//error_reporting($app['registry']->get("debug", false) ? E_ALL : 0);
		
		self::$_apps[$appname] = $app;
		
		return self::$_apps[$appname];
	}
	
	/**
	 * Initialize a new Raww app instance
	 *
	 * @param	string $route	Route
	 * @return	void
	 */
	public function handle($route) {
		
		$this["route"] = $route;

		$response = $this["router"]->dispatch($route);
		
		if(is_object($response) && method_exists($response, 'flush')) {
			
			$this["event"]->trigger("before_flush", array($response));
			$response->flush();

		} else {

			$response = $this['response']->assign(array(
				"body" => $this["tpl"]->render("views:error/404.php", array("message"=>$route)),
				"status" => "404"
			));
			
			$this["event"]->trigger("error_404", array("path"=>$route, "response" => $response));
			
			$response->flush();
		}
	}
}

// helper functions

function stripslashes_deep($value) {
	if ( is_array($value) ) {
		$value = array_map('\Raww\stripslashes_deep', $value);
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

function debug() {
	
	$vars   = func_get_args();           
	$_from  = debug_backtrace();
	$output = array();
	
	$output[] = '<div class="raww-debug">';
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