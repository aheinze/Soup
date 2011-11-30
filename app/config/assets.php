<?php

/* Assets */

// jQuery reference
$app["assets"]->addReference("jquery", array(
	"file" => "root:public/js/vendor/jquery.js"
));

// Handlebars reference
$app["assets"]->addReference("handlebars", array(
	"file" => "root:public/js/vendor/handlebars.js"
));

// jQuery reference
$app["assets"]->addReference("foundation", array(
	"file" => "root:public/css/foundation.pack.css"
));

$app["router"]->bind("#/assets/main\.(css|js)#", function($params) use($app) {

	$app["assets"]->register("main", array(
		//use the jquery reference
		array("file" => "ref:jquery"),
		// array("file" => "ref:handlebars"),
		array("file" => "ref:foundation"),
		array("file" => "root:public/css/base.css"),
		array("file" => "root:public/css/app.css"),
	));

	return $app["assets"]->dump("main", $params[":captures"][0], 0);
});