<?php

// base config

$app['registry']->set("debug", ($_SERVER['REMOTE_ADDR']==="127.0.0.1")); //enable debug mode on localhost


// routes

$app["router"]->bind("/", array("controller" => "Start")); // your entry point


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

/* Assets */

$app["assets"]->addReference("jquery", array(
	"file"   => "root:public/js/jquery.js",
	"minify" => false
));

$app["router"]->bind("#/assets/main\.(css|js)#", function($params) use($app) {

	$app["assets"]->register("main", array(
		//use the jquery reference
		array("file" => "ref:jquery"),
		array("file" => "root:public/css/base.css"),
		array("file" => "root:public/css/app.css"),
	));

	return $app["assets"]->dump("main", $params[":captures"][0], 0);
});