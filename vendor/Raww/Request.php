<?php

namespace Raww;

global $_PUT, $_DELETE;

if(Request::is("put")) {
	parse_str(file_get_contents('php://input'), $_PUT);
}

if(Request::is("delete")) {
	parse_str(file_get_contents('php://input'), $_DELETE);
}

class Request {
    

    /* mimeTypes */
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

    /* mobileDevices */
    public static $mobileDevices = array(
        "midp","240x320","blackberry","netfront","nokia","panasonic","portalmmm","sharp","sie-","sonyericsson",
        "symbian","windows ce","benq","mda","mot-","opera mini","philips","pocket pc","sagem","samsung",
        "sda","sgh-","vodafone","xda","iphone", "ipod","android"
    );


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
	
	public static function getUserAgent() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}
	
	public static function protocol() {
		return self::is("ssl") ? 'https' : 'http';
	}
	
	public static function getClientLang() {
		return strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
	}

    public static function getMimeType($val) {
        
        $parts = explode('.', $val);
        $extension = strtolower(array_pop($parts));

        return (isset(self::$mimeTypes[$extension]) ? self::$mimeTypes[$extension]:'text/html');
    }
	
	public static function get($index=null, $default = null) {
		
		if(!$index) return $_GET;
		
		return isset($_GET[$index]) ? $_GET[$index] : $default;
	}
	public static function post($index=null, $default = null) {
		
		if(!$index) return $_POST;
		
		return isset($_POST[$index]) ? $_POST[$index] : $default;
	}
	public static function put($index=null, $default = null) {
		
		global $_PUT;
		
		if(!$index) return $_PUT;
		
		return isset($_PUT[$index]) ? $_PUT[$index] : $default;
	}
	public static function delete($index=null, $default = null) {
		
		global $_DELETE;
		
		if(!$index) return $_DELETE;
		
		return isset($_DELETE[$index]) ? $_DELETE[$index] : $default;
	}
	
}