<?php

namespace Raww;

/**
 * Assets class. Combine, minify and apply filters to js and css files.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Assets extends \Raww\AppContainer {
  
	/**
	 * @var filters	Collection of asset filters
	 */
	public static $filters = array();

	protected $assets = array();
	protected $references = array();
	protected $dumped_references = array();

	/**
	 * Register an asset group
	 *
	 * @param	string $name	Name of the group to register
	 * @param	array $options	Containing files and filter options
	 * @return	void
	 */
	public function register($name,$options){

		$this->assets[$name] = $options;
	}

	/**
	 * Register an asset reference
	 *
	 * @param	string $name	Name of the reference
	 * @param	array $options	Containing file and filter options
	 * @return	void
	 */
	public function addReference($name,$options){
		$this->references[$name] = $options;
	}

	/**
	 * Dump an asset group (combine + apply filters)
	 *
	 * @param	string $name	Name of the asset group
	 * @param	string $type	(js|css) all css or js files of an asset group
	 * @param	int $cache_time	Cache lifetime (0 = no caching)
	 * @return	string
	 */
	public function dump($name, $type="js", $cache_time = 600){

		if(!isset($this->assets[$name])) return;

		$cache_key = "asset_".$name."_".$type;

		if($cache_time && $cached = $this->app['cache']->read($cache_key)) {
		  return $cached;
		}

		$output    = array();

		foreach ($this->assets[$name] as $asset) {

		  //handle references
		  if(substr($asset['file'], 0,4)=="ref:"){
		   
		   list($prefix, $ref_name) = explode(":", $asset['file']);
		   
		   if(!isset($this->references[$ref_name]) || isset($this->dumped_references[$ref_name])) continue;

		   $this->dumped_references[$ref_name] = true;
		   
		   $asset = $this->references[$ref_name];

		  }

		  $asset = array_merge(array(
			"filters"   => array('base64encode'),
			"ext"		=> strtolower(array_pop(explode(".", $asset['file']))),
			"base_path" => $_SERVER['SCRIPT_FILENAME'], 
			"base_url"	=> $this->app["base_url_path"]
		  ), $asset);

		  $file    = $asset['file'];
		  $ext     = $asset['ext'];
		  $content = '';

		  if (strpos($file, ':') !== false && $____file = $this->app['path']->get($file)) {
			 $asset['file'] = $file = $____file;
		  }

		  if($ext!=$type) continue;

		  switch ($ext) {
			
			case 'js':
			  
			  $content = file_get_contents($file);
			  
			  foreach($asset['filters'] as $filter){
				if(isset(self::$filters[$filter])){
					$content = call_user_func(self::$filters[$filter], $content, $asset);
				}
			  }
			  
			  break;

			case 'css':
			  
			  $content = file_get_contents($file);
			  
			  foreach($asset['filters'] as $filter){
				if(isset(self::$filters[$filter])){
					$content = call_user_func(self::$filters[$filter], $content, $asset);
				}
			  }
			  
			  $content = self::rewriteCssUrls($content, $asset);
			  
			  break;
			
			default:
			  continue;
		  }

		  $output[$type][] = $content;
		}

		$response = $this->app["response"]->assign(array(
		  'body' => implode("",$output[$type]),
		  'gzip' => true,
		  'mime' => $type,
		));

		if($cache_time) {
		  $this->app['cache']->write($cache_key, $response, $cache_time);
		}

		return $response;
	}

	/**
	 * Rewrite
	 *
	 * @param	string $content		content of css file
	 * @param	string $source_dir	dir of css file
	 * @param	int $base_path		app base path
	 * @return	string
	 */
	protected static function rewriteCssUrls($content, $asset){
		
		$base_path  = rtrim($asset['base_url'], '/');
		$source_dir = dirname($asset["file"]);
		$root_dir   = dirname($asset['base_path']);
		
		preg_match_all('/url\((.*)\)/',$content,$matches);

		$csspath  = "";

		if (strlen($root_dir) < strlen($source_dir)) {
		  $csspath = trim(str_replace($root_dir, '', $source_dir), "/")."/";
		} else {
		  # todo
		}

		foreach($matches[1] as $imgpath){
		  if(!preg_match("#^(http|/|data\:)#",trim($imgpath))){
			$content = str_replace('url('.$imgpath.')','url('.$base_path.'/'.$csspath.str_replace('"','',$imgpath).')',$content);
		  }
		}

		return $content;
	}

}

// Filters

Assets::$filters["process_css"] = function($str, $asset) {
	
	if($asset["ext"]!="css") return $str;

	/*
		@constants {
			constantName: constantValue;
		}
		.selector {
			propertyName: const(constantName);
		}
	*/
	if (preg_match_all('#@constants\s*\{\s*([^\}]+)\s*\}\s*#i', $str, $matches)) {
		
		$constants = array();
		
		foreach ($matches[0] as $i => $constant) {
			$str = str_replace($constant, '', $str);
			preg_match_all('#([_a-z0-9]+)\s*:\s*([^;]+);#i', $matches[1][$i], $vars);
			foreach ($vars[1] as $var => $name) {
				$constants["const($name)"] = $vars[2][$var];
			}
		}
		
		if (count($constants)) {
			$str = str_replace(array_keys($constants), array_values($constants), $str);
		}
	}
	
	
	/*
		@base(baseName) {
			propertyName: propertyValue;
			propertyName: propertyValue;
			propertyName: propertyValue;
		}
	
		.selector {
			based-on: base(baseName);
		}
	*/
	
	if (preg_match_all('#@base\(([^\s\{]+)\)\s*\{(\s*[^\}]+)\s*\}\s*#i', $str, $matches)) {
		
		$bases = array();
		
		$replace_bases = function($bases, $css, $current_base_name = false){
			// As long as there's based-on properties in the CSS string
			// Get all instances
			while (preg_match_all('#\s*based-on:\s*base\(([^;]+)\);#i', $css, $matches)) {
				// Loop through based-on instances
				foreach ($matches[0] as $key => $based_on) {
					$styles = '';
					$base_names = array();
					// Determine bases
					$base_names = preg_split('/[\s,]+/', $matches[1][$key]);
					// Loop through bases
					foreach ($base_names as $base_name) {
						// Looks like a circular reference, skip to next base
						if ($current_base_name && $base_name == $current_base_name) {
							$styles .= '/* RECURSION */';
							continue;
						}
						$styles .= $bases[$base_name];
					}

					// Insert styles this is based on
					$css = str_replace($based_on, $styles, $css);
				}
			}
			return $css;
		};
		
		// For each declaration
		foreach ($matches[0] as $key => $base) {
			// Remove the @base declaration
			$str = str_replace($base, '', $str);

			// Add declaration to our array indexed by base name
			$bases[$matches[1][$key]] = $matches[2][$key];
		}

		// Parse nested based-on properties, stopping at circular references
		foreach ($bases as $base_name => $properties) {
			$bases[$base_name] = $replace_bases($bases, $properties, $base_name);
		}
		
		if(count($bases)) {
			// Now apply replaced based-on properties in our CSS
			$str = $replace_bases($bases, $str);
		}
	}
	
	return $str;
};

Assets::$filters["minify_css"] = function($str, $asset) {
		
		if($asset["ext"]!="css") return $str;

		// Colons cannot be globally matched safely because of pseudo-selectors etc.
		$innerbrace = function($match) {
			return preg_replace('#\s*:\s*#', ':', $match[0]);
		};
		
		$str = preg_replace_callback( '#\{[^}]+\}#s', $innerbrace, trim($str));

		$replacements = array(
			'#\s{2,}#'                          => ' ',      // Remove double spaces
			'#\s*(;|,|\{)\s*#'                  => '$1',     // Clean-up around delimiters
			'#\s*;*\s*\}\s*#'                   => '}',      // Clean-up closing braces
			'#[^}{]+\{\s*}#'                    => '',       // Strip empty statements
			'#([^0-9])0[a-zA-Z%]{2}#'           => '${1}0',  // Strip unnecessary units on zeros
			'#:(0 0|0 0 0|0 0 0 0)([;}])#'      => ':0${2}', // Collapse zero lists
			'#(background-position):0([;}])#'   => '$1:0 0$2', // Restore any overshoot
			'#([^\d])0(\.\d+)#'                 => '$1$2',   // Strip leading zeros on floats
			'#(\[)\s*|\s*(\])|(\()\s*|\s*(\))#' => '${1}${2}${3}${4}',  // Clean-up bracket internal space
			'#\s*([>~+=])\s*#'                  => '$1',     // Clean-up around combinators
			'#\#([0-9a-f])\1([0-9a-f])\2([0-9a-f])\3#i' => '#$1$2$3', // Reduce Hex codes
		);

		return preg_replace(array_keys($replacements), array_values($replacements), $str);
};


Assets::$filters["minify_js"] = function($str, $asset) {
	
	if($asset["ext"]!="js") return $str;

	try {
		$minified = JSMin::minify($str);
	} catch(Exception $e) {
		$minified = $str;
	}
	
	return $minified;
};


/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.1 (2008-03-02)
 * @link https://github.com/rgrove/jsmin-php/
 */

class JSMin {
  const ORD_LF            = 10;
  const ORD_SPACE         = 32;
  const ACTION_KEEP_A     = 1;
  const ACTION_DELETE_A   = 2;
  const ACTION_DELETE_A_B = 3;

  protected $a           = '';
  protected $b           = '';
  protected $input       = '';
  protected $inputIndex  = 0;
  protected $inputLength = 0;
  protected $lookAhead   = null;
  protected $output      = '';

  // -- Public Static Methods --------------------------------------------------

  /**
   * Minify Javascript
   *
   * @uses __construct()
   * @uses min()
   * @param string $js Javascript to be minified
   * @return string
   */
  public static function minify($js) {
    $jsmin = new JSMin($js);
    return $jsmin->min();
  }

  // -- Public Instance Methods ------------------------------------------------

  /**
   * Constructor
   *
   * @param string $input Javascript to be minified
   */
  public function __construct($input) {
    $this->input       = str_replace("\r\n", "\n", $input);
    $this->inputLength = strlen($this->input);
  }

  // -- Protected Instance Methods ---------------------------------------------

  /**
   * Action -- do something! What to do is determined by the $command argument.
   *
   * action treats a string as a single character. Wow!
   * action recognizes a regular expression if it is preceded by ( or , or =.
   *
   * @uses next()
   * @uses get()
   * @throws JSMinException If parser errors are found:
   *         - Unterminated string literal
   *         - Unterminated regular expression set in regex literal
   *         - Unterminated regular expression literal
   * @param int $command One of class constants:
   *      ACTION_KEEP_A      Output A. Copy B to A. Get the next B.
   *      ACTION_DELETE_A    Copy B to A. Get the next B. (Delete A).
   *      ACTION_DELETE_A_B  Get the next B. (Delete B).
  */
  protected function action($command) {
    switch($command) {
      case self::ACTION_KEEP_A:
        $this->output .= $this->a;

      case self::ACTION_DELETE_A:
        $this->a = $this->b;

        if ($this->a === "'" || $this->a === '"') {
          for (;;) {
            $this->output .= $this->a;
            $this->a       = $this->get();

            if ($this->a === $this->b) {
              break;
            }

            if (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated string literal.');
            }

            if ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            }
          }
        }

      case self::ACTION_DELETE_A_B:
        $this->b = $this->next();

        if ($this->b === '/' && (
            $this->a === '(' || $this->a === ',' || $this->a === '=' ||
            $this->a === ':' || $this->a === '[' || $this->a === '!' ||
            $this->a === '&' || $this->a === '|' || $this->a === '?' ||
            $this->a === '{' || $this->a === '}' || $this->a === ';' ||
            $this->a === "\n" )) {

          $this->output .= $this->a . $this->b;

          for (;;) {
            $this->a = $this->get();

            if ($this->a === '[') {
              /*
                inside a regex [...] set, which MAY contain a '/' itself. Example: mootools Form.Validator near line 460:
                  return Form.Validator.getValidator('IsEmpty').test(element) || (/^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test(element.get('value'));
              */
              for (;;) {
                $this->output .= $this->a;
                $this->a = $this->get();

                if ($this->a === ']') {
                    break;
                } elseif ($this->a === '\\') {
                  $this->output .= $this->a;
                  $this->a       = $this->get();
                } elseif (ord($this->a) <= self::ORD_LF) {
                  throw new JSMinException('Unterminated regular expression set in regex literal.');
                }
              }
            } elseif ($this->a === '/') {
              break;
            } elseif ($this->a === '\\') {
              $this->output .= $this->a;
              $this->a       = $this->get();
            } elseif (ord($this->a) <= self::ORD_LF) {
              throw new JSMinException('Unterminated regular expression literal.');
            }

            $this->output .= $this->a;
          }

          $this->b = $this->next();
        }
    }
  }

  /**
   * Get next char. Convert ctrl char to space.
   *
   * @return string|null
   */
  protected function get() {
    $c = $this->lookAhead;
    $this->lookAhead = null;

    if ($c === null) {
      if ($this->inputIndex < $this->inputLength) {
        $c = substr($this->input, $this->inputIndex, 1);
        $this->inputIndex += 1;
      } else {
        $c = null;
      }
    }

    if ($c === "\r") {
      return "\n";
    }

    if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
      return $c;
    }

    return ' ';
  }

  /**
   * Is $c a letter, digit, underscore, dollar sign, or non-ASCII character.
   *
   * @return bool
   */
  protected function isAlphaNum($c) {
    return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
  }

  /**
   * Perform minification, return result
   *
   * @uses action()
   * @uses isAlphaNum()
   * @return string
   */
  protected function min() {
    $this->a = "\n";
    $this->action(self::ACTION_DELETE_A_B);

    while ($this->a !== null) {
      switch ($this->a) {
        case ' ':
          if ($this->isAlphaNum($this->b)) {
            $this->action(self::ACTION_KEEP_A);
          } else {
            $this->action(self::ACTION_DELETE_A);
          }
          break;

        case "\n":
          switch ($this->b) {
            case '{':
            case '[':
            case '(':
            case '+':
            case '-':
              $this->action(self::ACTION_KEEP_A);
              break;

            case ' ':
              $this->action(self::ACTION_DELETE_A_B);
              break;

            default:
              if ($this->isAlphaNum($this->b)) {
                $this->action(self::ACTION_KEEP_A);
              }
              else {
                $this->action(self::ACTION_DELETE_A);
              }
          }
          break;

        default:
          switch ($this->b) {
            case ' ':
              if ($this->isAlphaNum($this->a)) {
                $this->action(self::ACTION_KEEP_A);
                break;
              }

              $this->action(self::ACTION_DELETE_A_B);
              break;

            case "\n":
              switch ($this->a) {
                case '}':
                case ']':
                case ')':
                case '+':
                case '-':
                case '"':
                case "'":
                  $this->action(self::ACTION_KEEP_A);
                  break;

                default:
                  if ($this->isAlphaNum($this->a)) {
                    $this->action(self::ACTION_KEEP_A);
                  }
                  else {
                    $this->action(self::ACTION_DELETE_A_B);
                  }
              }
              break;

            default:
              $this->action(self::ACTION_KEEP_A);
              break;
          }
      }
    }

    return $this->output;
  }

  /**
   * Get the next character, skipping over comments. peek() is used to see
   *  if a '/' is followed by a '/' or '*'.
   *
   * @uses get()
   * @uses peek()
   * @throws JSMinException On unterminated comment.
   * @return string
   */
  protected function next() {
    $c = $this->get();

    if ($c === '/') {
      switch($this->peek()) {
        case '/':
          for (;;) {
            $c = $this->get();

            if (ord($c) <= self::ORD_LF) {
              return $c;
            }
          }

        case '*':
          $this->get();

          for (;;) {
            switch($this->get()) {
              case '*':
                if ($this->peek() === '/') {
                  $this->get();
                  return ' ';
                }
                break;

              case null:
                throw new JSMinException('Unterminated comment.');
            }
          }

        default:
          return $c;
      }
    }

    return $c;
  }

  /**
   * Get next char. If is ctrl character, translate to a space or newline.
   *
   * @uses get()
   * @return string|null
   */
  protected function peek() {
    $this->lookAhead = $this->get();
    return $this->lookAhead;
  }
}

// -- Exceptions ---------------------------------------------------------------
class JSMinException extends \Exception {}