<?php
    
Raww\Router::bind("/", array("controller" => "Start"));


Raww\Router::bind("#/assets/main\.(css|js)#", function($params){

	Raww\Assets\Manager::register("main", array(
		array("file" => "ref:jquery"),
		array("file" => "root:public/css/base.css"),
		array("file" => "root:public/css/app.css"),
	));
	
	return Raww\Assets\Manager::dump("main", $params[":captures"][0]);
});


\Raww\Assets\Manager::addReference("jquery", array(
	"file" => "root:public/js/jquery.js"
));