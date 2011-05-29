<?php

namespace Raww;

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

spl_autoload_register(function($resource){
	
	if(strpos($resource,__NAMESPACE__.'\\')===0) {
		
		$path = dirname(__DIR__).'/'.str_replace('\\', '/', $resource).'.php';

		if(file_exists($path)){
			require($path);
			return;
		}            
	}
});

class App extends DI{
	
	protected $name;
	
	protected static $_apps = array();
	
	public function __construct($name){
		
		parent::__construct();
		
		$this->name = $name;
	}

	public function name() {
		return $this->name;
	}
	
	public static function app($name) {
		return self::$_apps[$name];
	}
	
	public static function init($appname, $config) {
	
		Bench::start("rawwbench");
		
		if(!isset($config['base_url'])) {
			$config['base_url'] = implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
		}
		
		$app = new App($appname);
		
		$app["base_url"] = $config["base_url"];
		$app["path"]     = new Path($app);
		
		
		$app["session"]  = $app->share(function($app){ return new Session\Php($app); });
		$app["registry"] = $app->share(function($app){ return new Registry($app); });
		$app["router"]   = $app->share(function($app){ return new Router($app); });
		$app["event"]    = $app->share(function($app){ return new Event($app); });
		$app["tpl"]      = $app->share(function($app){ return new Template($app); });
		$app["i18n"]     = $app->share(function($app){ return new I18n($app); });
		$app["assets"]   = $app->share(function($app){ return new Assets($app); });
		$app["cache"]    = $app->share(function($app){ return new Cache\File($app); });
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
		
		
		require_once($app["path"]->get("config:bootstrap.php"));
		
		self::$_apps[$appname] = $app;
		
		return self::$_apps[$appname];
	}
	
	public function handle($route) {
		
		$this["route"] = $route;
		
		if(!isset($this["request"])) {
		
			$this["request"] = $this->share(function($app){
				return new \Raww\Request();
			});
		}

		error_reporting($this['registry']->get("debug", false) ? 0 : E_ALL);

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
		
		Bench::stop("rawwbench");
	}
}