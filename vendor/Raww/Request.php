<?php

namespace Raww;

/**
 * Request class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Request {
 
	/**
	 * @var $_POST	$_POST array
	 */
	public static $_POST = array();
	
	/**
	 * @var $_GET	$_GET array
	 */
	public static $_GET = array();
	
	/**
	 * @var $_COOKIE	$_COOKIE array
	 */
	public static $_COOKIE = array();
	
	/**
	 * @var $_REQUEST	$_REQUEST array
	 */
	public static $_REQUEST = array();
	
	/**
	 * @var $_PUT	$_PUT array
	 */
	public static $_PUT = array();
	
	/**
	 * @var $_DELETE	$_DELETE array
	 */
	public static $_DELETE = array();
	
	/**
	 * @var $mimeTypes	mimeTypes array
	 */
    public static $mimeTypes = array(
        'asc'   => 'text/plain',
        'au'    => 'audio/basic',
        'avi'   => 'video/x-msvideo',
        'bin'   => 'application/octet-stream',
        'class' => 'application/octet-stream',
        'css'   => 'text/css',
        'csv'	=> 'application/vnd.ms-excel',
        'doc'   => 'application/msword',
        'dll'   => 'application/octet-stream',
        'dvi'   => 'application/x-dvi',
        'exe'   => 'application/octet-stream',
        'htm'   => 'text/html',
        'html'  => 'text/html',
        'json'  => 'application/json',
        'js'    => 'application/x-javascript',
        'txt'   => 'text/plain',
        'bmp'   => 'image/bmp',
        'rss'   => 'application/rss+xml',
        'atom'  => 'application/atom+xml',
        'gif'   => 'image/gif',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'jpe'   => 'image/jpeg',
        'png'   => 'image/png',
        'ico'   => 'image/vnd.microsoft.icon',
        'mpeg'  => 'video/mpeg',
        'mpg'   => 'video/mpeg',
        'mpe'   => 'video/mpeg',
        'qt'    => 'video/quicktime',
        'mov'   => 'video/quicktime',
        'wmv'   => 'video/x-ms-wmv',
        'mp2'   => 'audio/mpeg',
        'mp3'   => 'audio/mpeg',
        'rm'    => 'audio/x-pn-realaudio',
        'ram'   => 'audio/x-pn-realaudio',
        'rpm'   => 'audio/x-pn-realaudio-plugin',
        'ra'    => 'audio/x-realaudio',
        'wav'   => 'audio/x-wav',
        'zip'   => 'application/zip',
        'pdf'   => 'application/pdf',
        'xls'   => 'application/vnd.ms-excel',
        'ppt'   => 'application/vnd.ms-powerpoint',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc'  => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'spl'   => 'application/x-futuresplash',
        'gtar'  => 'application/x-gtar',
        'gzip'  => 'application/x-gzip',
        'swf'   => 'application/x-shockwave-flash',
        'tar'   => 'application/x-tar',
        'xhtml' => 'application/xhtml+xml',
        'snd'   => 'audio/basic',
        'midi'  => 'audio/midi',
        'mid'   => 'audio/midi',
        'm3u'   => 'audio/x-mpegurl',
        'tiff'  => 'image/tiff',
        'tif'   => 'image/tiff',
        'rtf'   => 'text/rtf',
        'wml'   => 'text/vnd.wap.wml',
        'wmls'  => 'text/vnd.wap.wmlscript',
        'xsl'   => 'text/xml',
        'xml'   => 'text/xml'
    );

	/**
	 * @var $mobileDevices	mobileDevices array
	 */
    public static $mobileDevices = array(
        "midp","240x320","blackberry","netfront","nokia","panasonic","portalmmm","sharp","sie-","sonyericsson",
        "symbian","windows ce","benq","mda","mot-","opera mini","philips","pocket pc","sagem","samsung",
        "sda","sgh-","vodafone","xda","iphone", "ipod","android"
    );

	/**
	 * Check request properties
	 *
	 * @param	string $type	Name of the property (ajax|mobile|post|get|put|delete|ssl)
	 * @return	boolean
	 */
    public static function is($type){

        switch(strtolower($type)){
          case 'ajax':
            return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
            break;
          
          case 'mobile':
            return preg_match('/(' . implode('|',self::$mobileDevices). ')/i',strtolower($_SERVER['HTTP_USER_AGENT']));
            break;
          
          case 'post':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
            break;
          
          case 'get':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'get');
            break;
            
          case 'put':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'put');
            break;
            
          case 'delete':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'delete');
            break;
            
          case 'ssl':
            return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            break;
        }
        
        return false;
    }

	/**
	 * Get client ip
	 *
	 * @return	string
	 */
    public static function getClientIp(){

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            // Use the forwarded IP address, typically set when the
            // client is using a proxy server.
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
            // Use the forwarded IP address, typically set when the
            // client is using a proxy server.
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['REMOTE_ADDR'])){
            // The remote IP address
            return $_SERVER['REMOTE_ADDR'];
        }
    }

	/**
	 * Get site url
	 *
	 * @return	string
	 */
    public static function getSiteUrl() {
        $url = (self::is("ssl") ? 'https':'http')."://";
         
        if ($_SERVER["SERVER_PORT"] != "80") {
          $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
        } else {
          $url .= $_SERVER["SERVER_NAME"];
        }
        
        $url .= implode("/", array_slice(explode("/", $_SERVER['SCRIPT_NAME']), 0, -1));
        
        return rtrim($url,'/');
    }
	
	/**
	 * Get user agent
	 *
	 * @return	string
	 */
	public static function getUserAgent() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	
	/**
	 * Get protocol (http|https)
	 *
	 * @return	string
	 */
	public static function protocol() {
		return self::is("ssl") ? 'https' : 'http';
	}
	
	/**
	 * Get client user language (e.g. en or de)
	 *
	 * @return	string
	 */
	public static function getClientLang() {
		return strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
	}

	/**
	 * Get mimetype by extension
	 *
	 * @param	string $val	Extension (e.g. js, css etc)
	 * @return	string
	 */
    public static function getMimeType($val) {
        
        $parts = explode('.', $val);
        $extension = strtolower(array_pop($parts));

        return (isset(self::$mimeTypes[$extension]) ? self::$mimeTypes[$extension]:'text/html');
    }

	/**
	 * Get value of $_GET array 
	 *
	 * @param	string $index	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function get($index=null, $default = null) {
		
		return fetch_from_array(self::$_GET, $index, $default);
	}
	
	/**
	 * Get value of $_POST array 
	 *
	 * @param	string $index	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function post($index=null, $default = null) {
		
		return fetch_from_array(self::$_POST, $index, $default);
	}
	
	/**
	 * Get value of $_PUT array 
	 *
	 * @param	string $index	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function put($index=null, $default = null) {
		
		return fetch_from_array(self::$_PUT, $index, $default);
	}
	
	/**
	 * Get value of $_DELETE array 
	 *
	 * @param	string $index	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function delete($index=null, $default = null) {
		
		return fetch_from_array(self::$_DELETE, $index, $default);
	}
	
	/**
	 * Get value of $_REQUEST array 
	 *
	 * @param	string $index	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function requestvar($index=null, $default = null) {
		
		return fetch_from_array(self::$_REQUEST, $index, $default);
	}
	
	/**
	 * Get value of $_COOKIE array 
	 *
	 * @param	string $index	Index
	 * @param	mixed $default	default value if index not exists
	 * @return	mixed
	 */
	public static function cookie($index=null, $default = null) {
		
		return fetch_from_array(self::$_COOKIE, $index, $default);
	}
}

// init global vars

if(Request::is("put")) {
	parse_str(file_get_contents('php://input'), Request::$_PUT);
}

if(Request::is("delete")) {
	parse_str(file_get_contents('php://input'), Request::$_DELETE);
}

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {

	Request::$_POST    = array_map('\Raww\stripslashes_deep', $_POST);
	Request::$_GET     = array_map('\Raww\stripslashes_deep', $_GET);
	Request::$_COOKIE  = array_map('\Raww\stripslashes_deep', $_COOKIE);
	Request::$_REQUEST = array_map('\Raww\stripslashes_deep', $_REQUEST);

}else{
	Request::$_POST    = $_POST;
	Request::$_GET     = $_GET;
	Request::$_COOKIE  = $_COOKIE ;
	Request::$_REQUEST = $_REQUEST;
}
