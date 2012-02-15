<?php

namespace Soup;

/**
 * Socket class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Socket {

	protected $transport;

	/* available transport classes */
	protected $transports = array('\Soup\Socket\HttpCurl', '\Soup\Socket\HttpStreams', '\Soup\Socket\HttpSocket');


	/**
	 * Class Constructor.
	 *
	 * @return	void
	 */
	public function __construct() {

		// check available library support
		foreach ($this->transports as $classname) {
			$transport = new $classname();
			if ($transport->available()) {
				$this->transport = $transport;
				break;
			}
		}
	}	

	/**
	 * Execute a GET HTTP request
	 *
	 * @param	string $url	URL
	 * @param	array $options	Array of options
	 * @return	mixed
	 */
	public function get($url, $options = array()) {
		return $this->request($url, $options);
	}

	/**
	 * Execute a GET POST request
	 *
	 * @param	string $url	URL
	 * @param	array $data	Data to send as body
	 * @param	array $options	Array of options
	 * @return	mixed
	 */
	public function post($url, $data = null, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'POST', 'body' => $data), $options));
	}

	/**
	 * Execute a PUT HTTP request
	 *
	 * @param	string $url	URL
	 * @param	array $data	Data to send as body
	 * @param	array $options	Array of options
	 * @return	mixed
	 */	
	public function put($url, $data = null, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'PUT', 'body' => $data), $options));
	}
	
	/**
	 * Execute a DELETE HTTP request
	 *
	 * @param	string $url	URL
	 * @param	array $options	Array of options
	 * @return	mixed
	 */	
	public function delete($url, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'DELETE'), $options));
	}

	/**
	 * Execute a HTTP request
	 *
	 * @param	string $url	URL
	 * @param	array $options	Array of options
	 * @return	mixed
	 */
	public function request($url, $options = array()) {
		
		if ($this->transport) {
			return $this->transport->request($url, $options);
		}
		
		return false;
	}

}