<?php

//build foundation.pack.css

function minify_css($str) {
		
		// remove comments

		$regex = array(
			"`^([\t\s]+)`ism"=>'',
			"`^\/\*(.+?)\*\/`ism"=>"",
			"`([\n\A;]+)\/\*(.+?)\*\/`ism"=>"$1",
			"`([\n\A;\s]+)//(.+?)[\n\r]`ism"=>"$1\n",
			"`(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+`ism"=>"\n"
		);

		$str = preg_replace(array_keys($regex),$regex,$str);


		// Colons cannot be globally matched safely because of pseudo-selectors etc.
		$innerbrace = function($match) {
			return preg_replace('#\s*:\s*#', ':', $match[0]);
		};
		
		$str = preg_replace_callback( '#\{[^}]+\}#s', $innerbrace, trim($str));

		$replacements = array(
			'#\s{2,}#'                          => ' ',      // Remove double spaces
			'#\s*(;|,|\{)\s*#'                  => '$1',     // Clean-up around delimiters
			'#\s*;*\s*\}\s*#'                   => '}',      // Clean-up closing braces
			'#[^}{]+\{\s*}#'                    => '',       // Strip empty statements
			'#([^0-9])0[a-zA-Z%]{2}#'           => '${1}0',  // Strip unnecessary units on zeros
			'#:(0 0|0 0 0|0 0 0 0)([;}])#'      => ':0${2}', // Collapse zero lists
			'#(background-position):0([;}])#'   => '$1:0 0$2', // Restore any overshoot
			'#([^\d])0(\.\d+)#'                 => '$1$2',   // Strip leading zeros on floats
			'#(\[)\s*|\s*(\])|(\()\s*|\s*(\))#' => '${1}${2}${3}${4}',  // Clean-up bracket internal space
			'#\s*([>~+=])\s*#'                  => '$1',     // Clean-up around combinators
		);

		return preg_replace(array_keys($replacements), array_values($replacements), $str);
}


$files = array("globals.css", "typography.css", "grid.css", "ui.css", "forms.css", "mobile.css");
$pack  = array();
$dest  = __DIR__.'/../../foundation.pack.css';


foreach ($files as $file) {
    $pack[] = file_get_contents(__DIR__.'/'.$file);
}

@unlink($dest);

file_put_contents(__DIR__.'/../../foundation.pack.css', minify_css(implode("\n", $pack)));