<?php

/* Routes */


# define auto routes

/*
	
	$app["router"]->auto_route("/blog", __DIR__.'/../bundles');

	/blog/posts/show/34 will load __DIR__.'/../bundles/Blog/Controller/Posts' and call the action "show" with the parameter 34
*/

$app["router"]->auto_route("/app", __DIR__.'/../bundles');

# define special routes
$app["router"]->bind("/", array("controller" => "App\Controller\Start")); // your entry point

