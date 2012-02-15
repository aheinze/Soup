<?php

namespace Soup\Socket;

/*
	Class: Http
		HTTP base class
		Based on HTTP Socket connection class (http://cakephp.org, Cake Software Foundation, Inc., MIT License)		
*/
class Http {

    /*
		Variable: request
			Request defaults.
    */
	protected $request = array(
		'method' => 'GET',
		'version' => '1.1',
		'timeout' => 5,
		'redirects' => 5,
		'line' => null,
		'file' => null,
		'header' => array('Connection' => 'close', 'User-Agent' => 'Warp'),
		'body' => '',
		'cookies' => array(),
		'auth' => array('method' => 'Basic', 'user' => null, 'pass' => null),
		'raw' => null
	);

	/*
		Variable: response
			Response defaults.
	*/
	protected $response = array(
		'header' => array(),
		'body' => '',
		'cookies' => array(),
		'status' => array('http-version' => null, 'code' => null, 'reason-phrase' => null),
		'raw' => array('status-line' => null, 'header' => null, 'body' => null, 'response' => null)
	);

	/*
		Variable: line_break
			Line break.
	*/
	protected $line_break = "\r\n";

	/*
		Function: buildCookies
			 Builds cookie headers for a request

		Returns:
			String
	*/
	public function buildCookies($cookies) {
		$header = array();
		foreach ($cookies as $name => $cookie) {
			$header[] = $name.'='.$this->_escapeToken($cookie['value'], array(';'));
		}
		$header = $this->_buildHeader(array('Cookie' => $header), 'pragmatic');
		return $header;
	}

	/*
		Function: parseCookies
			 Parses cookies in response headers

		Returns:
			Array
	*/
	public function parseCookies($header) {

		if (!isset($header['Set-Cookie'])) {
			return false;
		}

		$cookies = array();
		foreach ((array) $header['Set-Cookie'] as $cookie) {
			if (strpos($cookie, '";"') !== false) {
				$cookie = str_replace('";"', "{__cookie_replace__}", $cookie);
				$parts  = str_replace("{__cookie_replace__}", '";"', explode(';', $cookie));
			} else {
				$parts = preg_split('/\;[ \t]*/', $cookie);
			}

			list($name, $value) = explode('=', array_shift($parts), 2);
			$cookies[$name] = compact('value');

			foreach ($parts as $part) {
				if (strpos($part, '=') !== false) {
					list($key, $value) = explode('=', $part);
				} else {
					$key = $part;
					$value = true;
				}

				$key = strtolower($key);
				if (!isset($cookies[$name][$key])) {
					$cookies[$name][$key] = $value;
				}
			}
		}

		return $cookies;
	}

	/*
		Function: _parseRequest
			 Parses the given http request url and options to build the http request string

		Returns:
			Array
	*/
	protected function _parseRequest($url, $options = array()) {

		$request = array_merge($this->request, array('url' => $this->_parseUrl($url)), $options);

		$request['timeout']   = (int) ceil($request['timeout']);
		$request['redirects'] = (int) $request['redirects'];
		
		if (is_array($request['header'])) {
			$request['header'] = $this->_parseHeader($request['header']);
			$request['header'] = array_merge(array('Host' => $request['url']['host']), $request['header']);
		}

		if (isset($request['auth']['user']) && isset($request['auth']['pass'])) {
			$request['header']['Authorization'] = $request['auth']['method'].' '.base64_encode($request['auth']['user'].':'.$request['auth']['pass']);
		}
		
		if (isset($request['url']['user']) && isset($request['url']['pass'])) {
			$request['header']['Authorization'] = $request['auth']['method'].' '.base64_encode($request['url']['user'].':'.$request['url']['pass']);
		}

		if (!empty($request['body']) && !isset($request['header']['Content-Type'])) {
			$request['header']['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		if (!empty($request['body']) && !isset($request['header']['Content-Length'])) {
			$request['header']['Content-Length'] = strlen($request['body']);
		}
		
		if (empty($request['line'])) {
			$request['line'] = strtoupper($request['method']).' '.$request['url']['path'].(isset($request['url']['query']) ? '?'.$request['url']['query'] : ''). ' HTTP/' . $request['version'].$this->line_break;
		}

		$request['raw'] = $request['line'].$this->_buildHeader($request['header']);

		if (!empty($request['cookies'])) {
			$request['raw'] .= $this->buildCookies($request['cookies']);
		}

		$request['raw'] .= $this->line_break.$request['body'];

		return $request;
	}

	/*
		Function: _parseResponse
			 Parses the given http response and breaks it down in parts

		Returns:
			Array
	*/
	protected function _parseResponse($res) {

		// set defaults
		$response = $this->response;
		$response['raw']['response'] = $res;

		// parse header
		if (preg_match("/^(.+\r\n)(.*)(?<=\r\n)\r\n/Us", $res, $match)) {
			
			list($null, $response['raw']['status-line'], $response['raw']['header']) = $match;
			$response['raw']['body'] = substr($res, strlen($match[0]));

			if (preg_match("/(.+) ([0-9]{3}) (.+)\r\n/DU", $response['raw']['status-line'], $match)) {
				$response['status']['http-version'] = $match[1];
				$response['status']['code'] = (int) $match[2];
				$response['status']['reason-phrase'] = $match[3];
			}

			$response['header'] = $this->_parseHeader($response['raw']['header']);
			$response['body']   = $response['raw']['body'];

			if (!empty($response['header'])) {
				$response['cookies'] = $this->parseCookies($response['header']);
			}
			
		} else {
			$response['body'] = $res;
			$response['raw']['body'] = $res;
		}

		if (isset($response['header']['Transfer-Encoding']) && $response['header']['Transfer-Encoding'] == 'chunked') {
			$response['body'] = $this->_decodeChunkedBody($response['body']);
		}

		foreach ($response['raw'] as $field => $val) {
			if ($val === '') {
				$response['raw'][$field] = null;
			}
		}

		return $response;
	}
	
	/*
		Function: _buildHeader
			 Builds the header string for a request

		Returns:
			String
	*/
	protected function _buildHeader($header, $mode = 'standard') {

		if (is_string($header)) {
			return $header;
		} elseif (!is_array($header)) {
			return false;
		}

		$returnHeader = '';
		foreach ($header as $field => $contents) {

			if (is_array($contents) && $mode == 'standard') {
				$contents = implode(',', $contents);
			}

			foreach ((array) $contents as $content) {
				$contents = preg_replace("/\r\n(?![\t ])/", "\r\n ", $content);
				$field = $this->_escapeToken($field);
				$returnHeader .= $field.': '.$contents.$this->line_break;
			}
		}

		return $returnHeader;
	}
	
	/*
		Function: _parseHeader
			 Parses an string based header to an array

		Returns:
			Array
	*/
	protected function _parseHeader($header) {

		if (is_array($header)) {
			foreach ($header as $field => $value) {
				unset($header[$field]);
				$field = strtolower($field);
				preg_match_all('/(?:^|(?<=-))[a-z]/U', $field, $offsets, PREG_OFFSET_CAPTURE);

				foreach ($offsets[0] as $offset) {
					$field = substr_replace($field, strtoupper($offset[0]), $offset[1], 1);
				}
				$header[$field] = $value;
			}
			return $header;
		} elseif (!is_string($header)) {
			return false;
		}

		preg_match_all("/(.+):(.+)(?:(?<![\t ])" . $this->line_break . "|\$)/Uis", $header, $matches, PREG_SET_ORDER);

		$header = array();
		foreach ($matches as $match) {
			list(, $field, $value) = $match;

			$value = trim($value);
			$value = preg_replace("/[\t ]\r\n/", "\r\n", $value);

			$field = $this->_unescapeToken($field);

			$field = strtolower($field);
			preg_match_all('/(?:^|(?<=-))[a-z]/U', $field, $offsets, PREG_OFFSET_CAPTURE);
			foreach ($offsets[0] as $offset) {
				$field = substr_replace($field, strtoupper($offset[0]), $offset[1], 1);
			}

			if (!isset($header[$field])) {
				$header[$field] = $value;
			} else {
				$header[$field] = array_merge((array)$header[$field], (array)$value);
			}
		}
		
		return $header;
	}

	/*
		Function: _decodeChunkedBody
			 Decodes a chunked message $body

		Returns:
			String
	*/
	protected function _decodeChunkedBody($body) {

		if (!is_string($body)) {
			return false;
		}

		$decodedBody = null;
		$chunkLength = null;

		while ($chunkLength !== 0) {

			// body is not chunked or is malformed
			if (!preg_match("/^([0-9a-f]+) *(?:;(.+)=(.+))?\r\n/iU", $body, $match)) {
				return $body;
			}

			$chunkSize = 0;
			$hexLength = 0;
			$chunkExtensionName = '';
			$chunkExtensionValue = '';
			if (isset($match[0])) {
				$chunkSize = $match[0];
			}
			if (isset($match[1])) {
				$hexLength = $match[1];
			}
			if (isset($match[2])) {
				$chunkExtensionName = $match[2];
			}
			if (isset($match[3])) {
				$chunkExtensionValue = $match[3];
			}

			$body = substr($body, strlen($chunkSize));
			$chunkLength = hexdec($hexLength);
			$chunk = substr($body, 0, $chunkLength);
			$decodedBody .= $chunk;
			
			if ($chunkLength !== 0) {
				$body = substr($body, $chunkLength + strlen("\r\n"));
			}
		}

		return $decodedBody;
	}

	/*
		Function: _parseUrl
			Parse a URL and return its components as array

		Returns:
			Array
	*/
	protected function _parseUrl($url) {

		// parse url
		$url = array_merge(array('user' => null, 'pass' => null, 'path' => '/', 'query' => null, 'fragment' => null), parse_url($url));
		
		// set scheme
		if (!isset($url['scheme'])) {
			$url['scheme'] = 'http';
		}

		// set host
		if (!isset($url['host'])) {
			$url['host'] = $_SERVER['SERVER_NAME'];
		}

		// set port
		if (!isset($url['port'])) {
			$url['port'] = $url['scheme'] == 'https' ? 443 : 80;
		}

		// set path
		if (!isset($url['path'])) {
			$url['path'] = '/';
		}
		
		return $url;
	}

	/*
		Function: _escapeToken
			Escapes a given $token according to RFC 2616 (HTTP 1.1 specs)

		Returns:
			String
	*/	
	protected function _escapeToken($token, $chars = null) {
		$regex = '/(['.join('', $this->_tokenEscapeChars(true, $chars)).'])/';
		$token = preg_replace($regex, '"\\1"', $token);
		return $token;
	}
	
	/*
		Function: _unescapeToken
			Unescapes a given $token according to RFC 2616 (HTTP 1.1 specs)

		Returns:
			String
	*/	
	protected function _unescapeToken($token, $chars = null) {
		$regex = '/"(['.join('', $this->_tokenEscapeChars(true, $chars)).'])"/';
		$token = preg_replace($regex, '\\1', $token);
		return $token;
	}
		
	/*
		Function: _tokenEscapeChars
			Gets escape chars according to RFC 2616 (HTTP 1.1 specs)

		Returns:
			Array
	*/	
	protected function _tokenEscapeChars($hex = true, $chars = null) {
		
		if (!empty($chars)) {
			$escape = $chars;
		} else {
			$escape = array('"', "(", ")", "<", ">", "@", ",", ";", ":", "\\", "/", "[", "]", "?", "=", "{", "}", " ");
			for ($i = 0; $i <= 31; $i++) {
				$escape[] = chr($i);
			}
			$escape[] = chr(127);
		}

		if ($hex == false) {
			return $escape;
		}

		$regexChars = '';

		foreach ($escape as $key => $char) {
			$escape[$key] = '\\x'.str_pad(dechex(ord($char)), 2, '0', STR_PAD_LEFT);
		}

		return $escape;
	}
	
}