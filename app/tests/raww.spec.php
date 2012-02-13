<?php

\Raww\Spec::describe("Raww", "Check Raww framework requirements.")->before(function($spec){

	$spec->app = $spec->persistent("app");

})->add("CacheFolderIsWritable", function($spec){
	
	$cachefolder = $spec->app["path"]->get("cache:");

	$spec->is_true(is_writable($cachefolder), $cachefolder." must be writable!");

})->add("LogFolderIsWritable", function($spec){

	$logfolder = $spec->app["path"]->get("log:");

	$spec->is_true(is_writable($logfolder), $logfolder." must be writable!");

})->add("ModRewriteIsEnabled", function($spec){

	$mod_rewrite = false;

	if (function_exists('apache_get_modules')) {
	  $modules = apache_get_modules();
	  $mod_rewrite = in_array('mod_rewrite', $modules);
	} else {
	  $mod_rewrite =  getenv('HTTP_MOD_REWRITE')=='On' ? true : false ;
	}

	$spec->is_true($mod_rewrite, "mod_rewrite is not enabled!");
});