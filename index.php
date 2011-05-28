<?php
    
	require_once(__DIR__."/vendor/Raww/App.php");
    
    Raww\App::init("rawwapp", array(       
	
		"paths"    => array(
            "root"    => __DIR__,
            "app"     => __DIR__.'/app',
            "vendor"  => __DIR__.'/vendor',
            "lib"     => __DIR__.'/app/libraries',
            "config"  => __DIR__.'/app/config',
            "views"   => __DIR__.'/app/views',
            "tmp"     => __DIR__.'/app/tmp',
            "cache"   => __DIR__.'/app/tmp/cache',
            "locale"  => __DIR__.'/app/locale',
            "modules" => __DIR__.'/app/modules',
        )
		
    ))->handle( $_SERVER['PATH_INFO'] ?: "/" );
    
    // var_dump(\Raww\Bench::get("rawwbench"));