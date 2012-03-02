<?php

/* Routes */


# define auto routes

/*
	
	$app["router"]->auto_route("/blog", 'Blog');

	/blog/posts/show/34 will load Blog\Controller\Posts' and call the action "show" with the parameter 34
*/

$app["router"]->auto_route("/app", 'App');

# define special routes
$app["router"]->bind("/", array("controller" => "App\Controller\Start")); // your entry point

