<?php
    
    if(!class_exists("\\Soup\\App")){
        require_once(__DIR__."/../vendor/Soup/App.php");
    }
    
    $app = Soup\App::init("Soupapp", array(         
		
        'debug'    => in_array($_SERVER["SERVER_NAME"], array('localhost','::1','127.0.0.1')), //enable debug mode on localhost
        'charset'  => 'UTF-8',
        'key'      => 'xxxxxxxxxxChangeMexxxxxx',
        'language' => 'en',
        
        //'timezone' => 'UTC',  //enable to auto set timezone

		"paths"    => array(

            // required path definitions
            
            "root"    => dirname(__DIR__),
            "lib"     => __DIR__.'/libraries',
            "modules" => __DIR__.'/modules',
            "locale"  => __DIR__.'/locale',
            "views"   => __DIR__.'/views',
            "tests"   => __DIR__.'/tests',
            "cache"   => __DIR__.'/tmp/cache',
            "log"     => __DIR__.'/tmp/log',
        )
    ));

    $app->load(__DIR__."/config/bootstrap.php");
    $app->load(__DIR__."/config/routes.php");
    $app->load(__DIR__."/config/assets.php");