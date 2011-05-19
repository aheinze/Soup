<?php
    
    namespace Raww;
	
    function init($config) {
        
		global $raww;
		
		$raww = new DI();
		
        Bench::start("rawwbench");
        
		if(!isset($config['base_url'])) $config['base_url'] = implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
        if(!isset($config['path'])) $config['path'] = "/";
        
        Registry::set("raww.path", $config['path']);
        Registry::set("raww.base_url", $config["base_url"]);
        
        Router::$base_url = $config["base_url"];
        
        Path::register("views", __DIR__.'/Raww/views');
        
        foreach($config['paths'] as $name => $path){
            Path::register($name, $path);
        }

        require_once(Path::get("config:bootstrap.php"));
        
        Event::trigger("raww.init");
        
        $response = Router::dispatch($config['path']);    
        
        if(is_object($response) && method_exists($response, 'flush')) {
            
            Event::trigger("raww.before_flush", array($response));
            $response->flush();

        } else {
            
            $response = new Response(template("views:error/404.php", array("message"=>$config['path'])), array(
                "status" => 404
            ));
            
            Event::trigger("raww.error.404", array("path"=>$config['path'], "response" => $response));
            
            $response->flush();
        }
        
        Bench::stop("rawwbench");
        
        Event::trigger("raww.shutdown", array($response));
    }    
    
    function import($resource, $once=true) {
        
        if($path = Path::get($resource)) {
            if($once){
				include_once($path);
            }else{
				include($path);
			}
			return true;
        }
        
        return false;
    }

    function template($view, $slots=array()){
        $tpl = new Template();

        return $tpl->render($view, $slots);
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
    
    spl_autoload_register(function($resource){
        
        if(strpos($resource,__NAMESPACE__.'\\')===0) {
            
            $path = __DIR__.'/'.str_replace('\\', '/', $resource).'.php';

            if(file_exists($path)){
                require($path);
                return;
            }            
        }
        
        // Autoload module and lib classes
        foreach(array('modules', 'lib') as $loc){
            if($path = Path::get("$loc:".str_replace('\\', '/', $resource).'.php')){
                require($path);
                return;
            }
        }
    });