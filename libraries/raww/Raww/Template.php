<?php

namespace Raww;


class Template {
    
	/* slots */
	protected $slots;
	

	public function render($____template, $_____slots = array()) {        
        
		$____layout  = false;
		
		if (strpos($____template, ' with ') !== false ) {
			list($____template, $____layout) = explode(' with ', $____template, 2);
		}
        
        if (strpos($____template, ':') !== false && $____file = Path::get($____template)) {
            $____template = $____file;
        }

        $extend = function($from) use(&$____layout) {
            $____layout = $from;
        };

		extract((array)$_____slots);
		
		ob_start();
		include $____template;
		$output = ob_get_clean();

		if ($____layout) {
		
			if (strpos($____layout, ':') !== false && $____file = Path::get($____layout)) {
				$____layout = $____file;
			}

			$content_for_layout = $output;
			
			ob_start();
			include $____layout;
			$output = ob_get_clean();
			
        }
		
		
		return $output;
	}

	function url($path, $echo=true) {
		$url = Router::url($path);

		if($echo) echo $url;
		
		return $url;
	}
}