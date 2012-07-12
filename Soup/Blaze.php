<?php

namespace Soup;

/**
 * Blaze class. Simple on the fly template parser class
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Blaze {
	
	protected static $allowed_calls = array(
		
		// string functions
		'explode','implode','strtolower','strtoupper','substr','stristr','strpos','print','print_r','number_format','htmlentities',
		'md5','strip_tags',

		// time functions
		'date','time','mktime',

		// math functions
		'round','trunc','rand','ceil','floor','srand',
	);


	protected $_line;
	protected $_pos;

	public static function render($__content, $__params = array(), $__sandbox=true){

		$__obj = new self();

		ob_start();

		$exec = function() use($__obj,$__content, $__params, $__sandbox){
			extract($__params);
			eval('?>'.$__obj->parse($__content, $__sandbox).'<?php ');
		};
		
		$exec();

		$output = ob_get_clean();

		return $output;
	}

	public static function render_file($file, $params = array(), $sandbox=true){

		return self::render(file_get_contents($file), $params, $sandbox);
	}

	public function parse($text, $sandbox=true) {

		return $this->compile($text, $sandbox);
	}

	protected function compile($text, $sandbox=true){

		// disable php in sandbox mode
		if ($sandbox) {
			$text = str_replace( array("<?","?>"), array("&lt;?","?&gt;"), $text);
		}

		$lines = explode("\n", $text);

		$noparse = false;
		$linenumber = 1;
		foreach ($lines as &$line) {
			
			$offset      = 0;
			$this->_line = &$line;

			while (($this->_pos = strpos($this->_line, "@", $offset)) !== false) {

				foreach (array('$', 'noparse','(','foreach', 'for', 'if', 'else', 'end', 'set') as $token) {
					
					if($this->isNextToken($token)){

						if($noparse && $token!="end"){
							continue 1;
						}

						switch($token) {
							
							case "$":
								
								$this->_line = substr_replace($this->_line, '@', $this->_pos, strlen($token)+1);

								$ln = 1;

								for($i=2;$i<strlen($this->_line)-$this->_pos;$i++){

									if($this->_line[$this->_pos+$i]!=";") {
										$ln++;
										continue;
									}

									$var = substr($this->_line, $this->_pos+1, $ln+1);

									$this->_line = substr_replace($this->_line, '<?php echo $'.$var.' ?>', $this->_pos, strlen($var)+1);
									break;
								}
								break;

							case "noparse":
								$this->_line = substr_replace($this->_line, ' ', $this->_pos, strlen($token)+1);

								$noparse = true;

								break;

							case "set":
								$this->_line = substr_replace($this->_line, '<?php ', $this->_pos, strlen($token)+2);

								if(($cmdend = strpos($this->_line, ');', $this->_pos))!==false) {
									$this->_line = substr_replace($this->_line, ' ?>', $cmdend, 3);
								}

								break;

							case "(":
								$this->_line = substr_replace($this->_line, '<?php echo(', $this->_pos, strlen($token)+1);

								if(($cmdend = strpos($this->_line, ');', $this->_pos))!==false) {
									$this->_line = substr_replace($this->_line, '); ?>', $cmdend, 3);
								}

								break;

							case "foreach":
							case "for":
							case "if":
								$this->_line = substr_replace($this->_line, '<?php '.$token, $this->_pos, strlen($token)+1);

								if(($cmdend = strpos($this->_line, '):', $this->_pos))!==false) {
									$this->_line = substr_replace($this->_line, ' { ?>', $cmdend+1, 3);
								}

								break;

							case "else":
								
								$this->_line = substr_replace($this->_line, '<?php } else { ?>', $this->_pos, strlen($token)+1);
								break;

							case "end":
								
								if($noparse){
									$this->_line = substr_replace($this->_line, ' ', $this->_pos, strlen($token)+1);
									$noparse = false;
								}else{
									$this->_line = substr_replace($this->_line, '<?php } ?>', $this->_pos, strlen($token)+1);
								}

								break;
							}

							if($sandbox && ($errors = $this->check_security($this->_line))) {
								return 'illegal call(s): '.implode(", ", $errors)." - on line ".$linenumber;
							}

						$offset = $this->_pos + 1;
						continue 2;
					}
				}

				$offset = $this->_pos+1;
			}

			$linenumber++;

		}

		$code = implode("\n", $lines);

		if($errors = $this->check_syntax($code)) {
			return implode("\n", $errors);
		}

		return $code;
	}

	protected function isNextToken($token) {
        return substr($this->_line, $this->_pos + 1, strlen($token)) == $token;
    }

    protected function check_security($code) {
		
		$tokens = token_get_all($code);
		$errors = array();

		foreach ($tokens as $index => $toc) {
			if(is_array($toc) && isset($toc[0])) {
				
				//var_dump($toc[0]);
				
				switch($toc[0]){

					case T_STRING:

						if(!in_array($toc[1], self::$allowed_calls)){
		            		
		            		$prevtoc = $tokens[$index-1];

							if(!isset($prevtoc[1]) || (isset($prevtoc[1]) &&$prevtoc[1]!='->')){
		            			
		            			$errors[] = $toc[1];
		            		}
		            	}
						break;

					case T_REQUIRE_ONCE: 
					case T_REQUIRE: 
					case T_NEW: 
					case T_RETURN: 
		            case T_BREAK: 
		            case T_CATCH: 
		            case T_CLONE: 
		            case T_EXIT: 
		            case T_PRINT: 
		            case T_GLOBAL: 
		            case T_INCLUDE_ONCE: 
		            case T_INCLUDE: 
		            case T_EVAL: 
		            case T_FUNCTION:
		            	if(!in_array($toc[1], self::$allowed_calls)){
		            		$errors[] = 'illegal call: '.$toc[1];
		            	}
		            	break;
				}
			}
		}

		return count($errors) ? $errors:false;
    }

    protected function check_syntax($code){

    	$errors = array();

	    ob_start();

		$check = eval('?>'.'<?php if(0): ?>'.$code.'<?php endif; ?><?php ');

	    if ($check === false) {
	        $output = ob_get_clean();
	        $output = strip_tags($output);

	        if (preg_match_all("/on line (\d+)/m", $output, $matches)) {

	            foreach($matches[1] as $m){
	            	$errors[] = "Parse error on line: ".$m;
	            }
	            
	        } else {
	        	$errors[] = 'syntax error';
	        }

	    } else {
	        ob_end_clean();
	    }

	    return count($errors) ? $errors:false;
	}
}