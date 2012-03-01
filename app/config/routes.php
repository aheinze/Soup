<?php

/* Routes */

$app["router"]->bind("/", array("controller" => "App\Controller\Start")); // your entry point

$app["router"]->auto_route("/app", __DIR__.'/../bundles');
