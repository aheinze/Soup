<?php
    
    if(!class_exists("\\Soup\\App")){
        require_once(__DIR__."/../Soup/App.php");
    }
    
    $app = Soup\App::init("Soupapp", array(         
		
		"paths"    => array(
            // required path definitions
            "locale"  => __DIR__.'/locale',
            "views"   => __DIR__.'/views',
            "tests"   => __DIR__.'/tests',
            "cache"   => __DIR__.'/tmp/cache',
            "log"     => __DIR__.'/tmp/log',
        ),

        'autoload' => array(
            'directories' => array(__DIR__.'/libraries'),
            'namespaces'  => array('Bundle' => __DIR__.'/bundles')
        ),
    ));

    $app->load(__DIR__."/config/bootstrap.php");