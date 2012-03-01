<?php

/* Routes */


# define auto routes
$app["router"]->auto_route("/app", __DIR__.'/../bundles');

# define special routes
$app["router"]->bind("/", array("controller" => "App\Controller\Start")); // your entry point

