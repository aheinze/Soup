<?php

namespace Raww;

/**
 * Response class.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Response {
    
    public $body    = '';
    public $status  = 200;
    public $mime    = 'html';
    public $gzip    = false;
    public $nocache = false;
	
	/* statusCodes */
	public static $statusCodes = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
		// Successful 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Request Range Not Satisfiable',
		417 => 'Expectation Failed',
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);
    
    protected $_headers = array();
    
	/**
	 * Class constructor
	 *
	 * @param	string $body	
	 * @param	string $options	
	 * @return	void
	 */
    public function __construct($body=null, $options = array()){
        
        $options = array_merge(array("body" => $body), $options);
        
		$this->assign($options);
    }
	
	/**
	 * Assign response options
	 *	
	 * @param	string $options	
	 * @return	object
	 */
	public function assign($options = array()){
        if(count($options)){
            foreach($options as $name => $value) {
                $this->{$name} = $value;
            }
        }
		
		return $this;
	}
    
	/**
	 * Prints response body
	 *
	 * @return	void
	 */
    public function send() {
        
        if($this->gzip && !ob_start("ob_gzhandler")) ob_start();

        if(!headers_sent($filename, $linenum)){
	        if($this->nocache){
	        	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
				header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	  			header('Pragma: no-cache');
	        }
	        
			header('HTTP/1.0 '.$this->status.' '.self::$statusCodes[$this->status]);
	        header('Content-type: '.Request::getMimeType($this->mime));
        
        }
        
        echo $this->body;  
    }    
}