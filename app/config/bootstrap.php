<?php

$app['debug'] = in_array($_SERVER["SERVER_NAME"], array('localhost','::1','127.0.0.1')); //enable debug mode on localhost


//$app['session']->init();	// uncomment to auto init session
//$app['timezone'] = 'UTC';  //enable to auto set timezone




//include further configuration
include_once(__DIR__."/storage.php");
include_once(__DIR__."/routes.php");
include_once(__DIR__."/assets.php");