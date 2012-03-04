<?php

/* Assets */

// jQuery reference
$app["assets"]->addReference("jquery", array(
	"file" => "root:assets/jquery.js"
));

// Foundation reference
$app["assets"]->addReference("base.css", array(
	"file" => "root:assets/base.css"
));

//bind route to /assets/main.(js|css)

$app["assets"]->auto_route("main", array(
	//add references
	array("file" => "ref:soup"),
	array("file" => "ref:jquery"),
	array("file" => "ref:base.css"),

	array("file" => "root:js/app.js"),
	array("file" => "root:css/app.css"),

), $app["debug"] ? 0:600);

//bind route to /assets/bootstrap.(js|css)

$app["assets"]->auto_route("bootstrap", array(
	//add references
	array("file" => "ref:jquery"),

	array("file" => "root:assets/bootstrap/js/bootstrap.min.js"),
	array("file" => "root:assets/bootstrap/css/bootstrap.min.css"),
	array("file" => "root:assets/bootstrap/css/bootstrap-responsive.min.css"),

), $app["debug"] ? 0:600);