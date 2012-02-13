<?php

/* Assets */

// jQuery reference
$app["assets"]->addReference("jquery", array(
	"file" => "root:public/js/vendor/jquery.js"
));

// Foundation reference
$app["assets"]->addReference("base.css", array(
	"file" => "root:public/css/base.css"
));

//bind route to /assets/main.(js|css)

$app["assets"]->auto_route("main", array(
	//add references
	array("file" => "ref:jquery"),
	array("file" => "ref:base.css"),

	array("file" => "root:public/js/app.js"),
	array("file" => "root:public/css/app.css"),

), $app["debug"] ? 0:600);