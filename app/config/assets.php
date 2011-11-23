<?php

/* Assets */

// jQuery reference
$app["assets"]->addReference("jquery", array(
	"file" => "root:public/js/vendor/jquery.js"
));

// Backbone reference
$app["assets"]->addReference("backbone", array(
	"file" => "root:public/js/vendor/backbone.js"
));

$app["router"]->bind("#/assets/main\.(css|js)#", function($params) use($app) {

	$app["assets"]->register("main", array(
		//use the jquery reference
		array("file" => "ref:jquery"),
		array("file" => "ref:backbone"),
		array("file" => "root:public/css/base.css"),
		array("file" => "root:public/css/app.css"),
	));

	return $app["assets"]->dump("main", $params[":captures"][0], 0);
});