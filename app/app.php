<?php
    
    if(!class_exists("\\Soup\\App")){
        require_once(__DIR__."/../Soup/App.php");
    }
    
    $app = Soup\App::init("Soupapp", array(         
        
        'debug'     => in_array($_SERVER["SERVER_NAME"], array('localhost','::1','127.0.0.1')),   //enable debug mode on localhost
        'timezone'  => 'UTC',
        'charset'   => 'UTF-8',
        'key'       => 'xxxxxChangeMePleasexxxxx',
        'language'  => 'en',

        "paths" => array(
            // required path definitions
            "locale"  => __DIR__.'/locale',
            "views"   => __DIR__.'/views',
            "tests"   => __DIR__.'/tests',
            "cache"   => __DIR__.'/tmp/cache',
            "log"     => __DIR__.'/tmp/log'
        ),

        'autoload' => array(
            'directories' => array(__DIR__.'/libraries', __DIR__.'/bundles'),
            'namespaces'  => array(
                'App' => __DIR__.'/bundles/App'
            )
        ),
    ));

    //$app['session']->init();  // uncomment to auto init session

    //include further configuration
    include_once(__DIR__."/config/storage.php");
    include_once(__DIR__."/config/routes.php");
    include_once(__DIR__."/config/assets.php");