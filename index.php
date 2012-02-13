<?php
    
    require_once(__DIR__."/app/app.php");

    $app->handle( isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : "/" );