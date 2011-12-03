<?php

namespace Raww;

/**
 * View class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class View extends AppContainer {
    
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
	protected function base_url($path, $echo=true) {
		$url = $this->app["router"]->base_url($path);

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
		$url = $this->app["router"]->url($path);

		if($echo) echo $url;
		
		return $url;
	}
	
	/**
	 * Url helper function to build links within a template
	 *
	 * @param	string $name	
	 * @param	string $path	
	 * @param	array $options	
	 * @param	boolean $echo	
	 * @return	string
	 */
	protected function link($name, $path, $options=array(), $echo=true) {
		
		$attributes = array();
		
		foreach($options as $key=>$value) {
			$attributes[] = "{$key}=\"{$value}\"";
		}
		
		$link = '<a href="'.$this->url($path, false).'" '.implode(' ', $attributes).'>'.$name.'</a>';
		
		if($echo) echo $link;
		
		return $link;
	}

	protected function get($index=null, $default = null) {
		return $this->app['request']->get($index, $default);
	}
	protected function post($index=null, $default = null) {
		return $this->app['request']->post($index, $default);
	}
	protected function put($index=null, $default = null) {
		return $this->app['request']->put($index, $default);
	}
	protected function delete($index=null, $default = null) {
		return $this->app['request']->delete($index, $default);
	}
}