<?php
    
    namespace Raww;
    
    function init($config) {
        
        if(!isset($config['path'])) $config['path'] = "/";
        
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
        
        Event::trigger("raww.shutdown", array($response));
    }
    
    
    function import($resource) {
        
        if($path = Path::get($resource)) {
            include_once($path);
            return true;
        }
        
        return false;
    }

    function template($view, $slots){
        $tpl = new Template();

        return $tpl->render($view, $slots);
    }
    
    
    function debug($var = false) {
                      
        $_from  = debug_backtrace();
        $output = array();
        
        $output[] = '<div class="raww-debug">';
          $output[] = '<strong title="'.$_from[0]['file'].'">' . $_from[0]['file'] . '</strong>';
          $output[] = ' (line: <strong>' . $_from[0]['line'] . '</strong>)';
          $output[] = '<pre>';
          $output[] = print_r($var, true);
          $output[] = "</pre>";
        $output[] = '<div>';

        echo implode("\n",$output);
	}
    
    spl_autoload_register(function($resource){
        
        if(strpos($resource,__NAMESPACE__.'\\')===0) {
            
            $path = __DIR__.'/'.str_replace('\\', '/', $resource).'.php';

            if(file_exists($path)){
                
                include_once($path);
                return;
            }            
        }
        
        // Autoload module classes
        if($path = Path::get("modules:".str_replace('\\', '/', $resource).'.php')){
            include_once($path);
        }

        // Autoload module classes
        if($path = Path::get("lib:".str_replace('\\', '/', $resource).'.php')){
            include_once($path);
        }

    });