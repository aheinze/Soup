<?php

/* Assets */

// jQuery reference
$app["assets"]->addReference("jquery", array(
	"file" => "root:assets/jquery.js"
));

// Soup app helper
$app["assets"]->addReference("soup.app.helper", array(
	"file" => "root:assets/app.js"
));

//bind route to /assets/main.(js|css)

$app["assets"]->auto_route("system", array(
	//add references
	array("file" => "ref:soup"),
	array("file" => "ref:jquery"),
	array("file" => "ref:soup.app.helper"),

	// foundation framework
	array("file" => "root:assets/foundation/foundation.css"),
	array("file" => "root:assets/foundation/modernizr.foundation.js"),
	array("file" => "root:assets/foundation/foundation.js"),

	array("file" => "root:assets/topbox/topbox.js"),
	array("file" => "root:assets/topbox/topbox.css"),

	array("file" => "root:assets/modal/modal.js"),
	array("file" => "root:assets/modal/modal.css"),


	array("file" => "root:css/bootstrap.css"),
	array("file" => "root:js/bootstrap.js"),

	array("file" => "root:assets/animate.css"),
	
), $app["debug"] ? 0:600);