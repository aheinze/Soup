<?php

namespace Raww;

class Socket {

	protected $transport;

	/* available transport classes */
	protected $transports = array('\Raww\Socket\HttpCurl', '\Raww\Socket\HttpStreams', '\Raww\Socket\HttpSocket');

	/*
		Function: Constructor
			Class Constructor.
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

	/*
		Function: get
			Execute a GET HTTP request

		Parameters:
			$url - URL
			$options - Array of options

		Returns:
			Mixed
	*/	
	public function get($url, $options = array()) {
		return $this->request($url, $options);
	}

	/*
		Function: post
			Execute a POST HTTP request

		Parameters:
			$url - URL
			$data - Data to send as body
			$options - Array of options

		Returns:
			Mixed
	*/	
	public function post($url, $data = null, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'POST', 'body' => $data), $options));
	}

	/*
		Function: put
			Execute a PUT HTTP request

		Parameters:
			$url - URL
			$data - Data to send as body
			$options - Array of options

		Returns:
			Mixed
	*/	
	public function put($url, $data = null, $options = array()) {
		return $this->request($url, array_merge(array('method' => 'PUT', 'body' => $data), $options));
	}

	/*
		Function: request
			Execute a HTTP request

		Parameters:
			$url - URL
			$options - Array of options

		Returns:
			Mixed
	*/
	public function request($url, $options = array()) {
		
		if ($this->transport) {
			return $this->transport->request($url, $options);
		}
		
		return false;
	}

}