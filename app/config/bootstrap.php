<?php

// base config

//set default timezone
//date_default_timezone_set('UTC');

$app['debug'] = in_array($_SERVER["SERVER_NAME"], array('localhost','::1','127.0.0.1')); //enable debug mode on localhost

$app['session']->init();

// db connection
/*
$app["con:default"] = $app->share(function() {  // default
	
	return new Raww\Connection\Pdo(array(
      'dns'       => 'mysql:host=127.0.0.1;dbname=DBNAME;port=3306',
      'user'      => 'USER',
      'password'  => 'xxx',
      'options'   => array()
	));
	
});
*/

// include routes and assets definition
include_once(__DIR__.'/routes.php');
include_once(__DIR__.'/assets.php');