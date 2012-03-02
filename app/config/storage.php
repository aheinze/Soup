<?php

// db storage

$app->share("storage:default", function($app) {  // default mysql
	
	return new \Soup\Connection\Pdo(array(
      'dns'       => 'mysql:host=127.0.0.1;dbname=DBNAME;port=3306',
      'user'      => 'USER',
      'password'  => 'xxx',
      'options'   => array()
	));
});

$app->share("storage:redis", function($app) {  // redis

	return new \Soup\Connection\Redis('127.0.0.1', 6379);
});