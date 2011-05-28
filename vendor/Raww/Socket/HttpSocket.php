<?php

namespace Raww\Socket;

/*
	Class: HttpSocket
		HTTP transport class using fsockopen
*/
class HttpSocket extends Http {

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

		// set host
		$host = $request['url']['scheme'] == 'https' ? sprintf('ssl://%s', $request['url']['host']) : $request['url']['host'];
		
		// connect with fsockopen
		$res = false;
	    $fp  = @fsockopen($host, $request['url']['port'], $errno, $errstr, $request['url']['timeout']);
	    if ($fp !== false) {
	        @fwrite($fp, $request['raw']);
	        while (!feof($fp)) {
	            $res .= fgets($fp, 4096);
	        }
	        @fclose($fp);
	    }

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
		return function_exists('fsockopen');
	}

}