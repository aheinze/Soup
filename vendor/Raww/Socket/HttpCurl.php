<?php

namespace Raww\Socket;

/*
	Class: HttpCurl
		HTTP transport class using cURL
*/
class HttpCurl extends Http {
	
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

		// parse request
		$request = $this->_parseRequest($url, $options);

		// set curl options
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, $request['version'] == '1.0' ? CURL_HTTP_VERSION_1_0 : CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $request['timeout']);
		curl_setopt($curl, CURLOPT_TIMEOUT, $request['timeout']);
		curl_setopt($curl, CURLOPT_MAXREDIRS, $request['redirects']);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		// post request ?
		if ($request['method'] == 'POST') {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request['body']);
		}

		// put request ?
		if ($request['method'] == 'PUT') {
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request['method']);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request['body']);
		}
		
		// connect with curl
		$res = curl_exec($curl);
		curl_close($curl);

		// parse response
		$res = $this->_parseResponse($res);
	
		// save to file
		if ($res && $request['file'] && file_put_contents($request['file'], $res['body']) === false) {
			return false;
		}
		
		return $res;
	}

	/*
		Function: available
			Check if HTTP request method is available

		Returns:
			Boolean
	*/
	public function available() {
		return function_exists('curl_init');
	}

}