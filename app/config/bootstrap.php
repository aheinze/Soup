<?php

// uncomment to auto init session

//$app['session']->init();

// db storage

$app->self_share("storage:default", function($app) {  // default
	
	return new \Soup\Connection\Pdo(array(
      'dns'       => 'mysql:host=127.0.0.1;dbname=DBNAME;port=3306',
      'user'      => 'USER',
      'password'  => 'xxx',
      'options'   => array()
	));
});

// include routes and assets definition
include_once(__DIR__.'/routes.php');
include_once(__DIR__.'/assets.php');