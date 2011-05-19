<?php
    
    require_once(__DIR__."/libraries/raww/bootstrap.php");
    
    Raww\init(array(
        "path"     => ($_SERVER['PATH_INFO'] ?: "/"),        
        "paths"    => array(
            "root"    => __DIR__,
            "app"     => __DIR__.'/app',
            "lib"     => __DIR__.'/libraries',
            "config"  => __DIR__.'/app/config',
            "views"   => __DIR__.'/app/views',
            "tmp"     => __DIR__.'/app/tmp',
            "cache"   => __DIR__.'/app/tmp/cache',
            "locale"  => __DIR__.'/app/locale',
            "modules" => __DIR__.'/app/modules',
        )
    ));