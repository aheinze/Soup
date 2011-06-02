<?php

namespace Raww;

/**
 * Template class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Template extends AppContainer {
    
	/* slots */
	protected $slots;
	
	/**
	 * Render a template file
	 *
	 * @param	string $____template	Template path
	 * @param	string $_____slots	passed vars
	 * @return	string
	 */
	public function render($____template, $_____slots = array()) {        
        
		$____layout  = false;
		
		if (strpos($____template, ' with ') !== false ) {
			list($____template, $____layout) = explode(' with ', $____template, 2);
		}
        
        if (strpos($____template, ':') !== false && $____file = $this->app["path"]->get($____template)) {
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
		
			if (strpos($____layout, ':') !== false && $____file = $this->app["path"]->get($____layout)) {
				$____layout = $____file;
			}

			$content_for_layout = $output;
			
			ob_start();
			include $____layout;
			$output = ob_get_clean();
			
        }
		
		
		return $output;
	}

	/**
	 * Url helper function to build urls within a template
	 *
	 * @param	string $path	
	 * @param	boolean $echo	
	 * @return	string
	 */
	protected function baseurl($path, $echo=true) {
		$url = $this->app["router"]->baseurl($path);

		if($echo) echo $url;
		
		return $url;
	}
	
	/**
	 * Url helper function to build route within a template
	 *
	 * @param	string $path	
	 * @param	boolean $echo	
	 * @return	string
	 */
	protected function url($path, $echo=true) {
		$url = $this->app["router"]->route($path);

		if($echo) echo $url;
		
		return $url;
	}
}