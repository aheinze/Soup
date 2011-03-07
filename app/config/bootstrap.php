<?php

use Raww\Router;
use Raww\Assets\Manager as AssetsManager;

 
Router::bind("/", array("controller" => "Start")); // your entry point

/* Assets */

AssetsManager::addReference("jquery", array(
	"file" => "root:public/js/jquery.js"
));

Router::bind("#/assets/main\.(css|js)#", function($params){

	AssetsManager::register("main", array(
		//use the jquery reference
		array("file" => "ref:jquery"),
		array("file" => "root:public/css/base.css"),
		array("file" => "root:public/css/app.css"),
	));

	return AssetsManager::dump("main", $params[":captures"][0], 0);
});