<?php
    
    if(!class_exists("\\Raww\\App")){
        require_once(__DIR__."/../vendor/Raww/App.php");
    }
    
    $app = Raww\App::init("rawwapp", array(         
		
		// "base_route_path" => $_SERVER['SCRIPT_NAME'], // enable if mod_rewrite is disabled
		
		"paths"    => array(

            // required path definitions
            
            "root"    => dirname(__DIR__),
            "config"  => __DIR__.'/config',
            "lib"     => __DIR__.'/libraries',
            "modules" => __DIR__.'/modules',
            "locale"  => __DIR__.'/locale',
            "views"   => __DIR__.'/views',
            "tests"   => __DIR__.'/tests',
            "tmp"     => __DIR__.'/tmp',
            "cache"   => __DIR__.'/tmp/cache',
            "log"     => __DIR__.'/tmp/log',
            
            // app specific
            // e.g
            // "plugins" => __DIR__.'/plugins',
        )
		
    ));

    $app->load("config:bootstrap.php");