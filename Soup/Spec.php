<?php

namespace Soup;

/**
 * Spec class. Test tool.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Spec {
    
    public static $specs   = array();
    public static $globals = array();

    public $name;
    public $description;
    
    protected $tests;
    protected $total;
    protected $passed;
    protected $failed;
    

    // Static

    public static function describe($name, $description = "") {
        
        static::$specs[$name] = new static($name, $description);

        return static::$specs[$name];
    }

    public static function exists($name) {
        
        return isset(static::$specs[$name]);
    }

    public static function run($name, $renderer=null) {
        
        if(isset(static::$specs[$name])){
            return static::$specs[$name]->execute($renderer);
        }
    }

    public static function persistent($name, $value=null) {
        
        if(is_null($value) && isset(self::$globals[$name])){
            return self::$globals[$name];
        }else{
            self::$globals[$name] = $value;
        }
    }

    public static function run_all($renderer=null) {
        
        $results = array();
        foreach(static::$specs as $name=>$spec){
            $results[$name] = $spec->run($name, $renderer);
        }
        return $results;
    }

    public static function load_from_folder($folder, $renderer=null) {
        
        $folder   = rtrim($folder, "\\/");

        if(!file_exists($folder)){
            return;
        }

        $iterator = new \RecursiveDirectoryIterator($folder);

        foreach($iterator as $file) {
           
           if($file->isFile() && substr($file->getFilename(), -9)==".spec.php") {
              include($folder.'/'.$file->getFilename());
           }
        }

        if($renderer){
            static::run_all($renderer);
        }
    }


    // Object
    
    public function __construct($name, $description) {
        $this->tests       = array();
        $this->name        = $name;
        $this->description = $description;

        //events
        $this->before = function($spec){};
        $this->after  = function($spec){};
    }

    public function before($closure) {
       $this->before = $closure; 

       return $this;
    }

    public function after($closure) {
       $this->after = $closure;

       return $this;
    }

    public function add($name, $check) {
        
        $this->tests[$name] = $check;
        return $this;
    }

    public function assert() {
        return new Assert();
    }

    public function execute($renderer=null) {

        $output = array(
            'name' => $this->name,
            'description' => $this->description,
            'duration'    => 0,
            'passed' => 0,
            'failed' => 0,
            'total'  => 0,
            'tests'  => array(),
        );

        $this->total       = 0;
        $this->passed      = 0;
        $this->failed      = 0;

        $start = microtime();

        call_user_func($this->before, $this);

        foreach ($this->tests as $name => $check) {
            
            $test = array(
                'passed' => 1,
                'message'=> ''
            );

            try {
                $result = $check($this);
                $this->passed++;
            } catch (\Exception $exception) {
                
                $test['file']  = "n/a";
                $test['line']  = "n/a";

                $trace = $exception->getTrace();

                for($i=0;$i<count($trace);$i++){
                    if(strpos($trace[$i]["file"], DIRECTORY_SEPARATOR.'Spec.php')===false){
                        $test['file']  = $trace[$i]["file"];
                        $test['line']  = $trace[$i]["line"];
                        break; 
                    }
                }

                $test['passed']  = 0;
                $test['message'] = (string) $exception;
                $this->failed++;
            }

            $this->total++;
            $output['tests'][$name] = $test;
        }

        call_user_func($this->after, $this);

        $output["duration"] = microtime() - $start;

        $output["passed"] = $this->passed;
        $output["failed"] = $this->failed;
        $output["total"]  = $this->total;

        if(is_callable($renderer)){
            call_user_func($renderer, $output);
        }


        return $output;
    }

    // Asserts 
    
    public static function equals($value, $subject, $message){
        self::must($subject == $value, $message);
    }

    public static function not_equals($value, $subject, $message){ 
        self::must($subject != $value, $message); 
    }
    
    public static function strict_equals($value, $subject, $message){ 
        self::must($subject === $value, $message); 
    }

    public static function strict_not_equals($value, $subject, $message){ 
        self::must($subject !== $value, $message); 
    }

    public static function is_true($subject, $message)
    { self::strict_equals(true, $subject, $message); }

    public static function is_not_true($subject, $message){ 
        self::strict_not_equals(true, $subject, $message); 
    }

    public static function is_false($subject, $message){ 
        self::strict_equals(false, $subject, $message); 
    }

    public static function is_not_false($subject, $message){ 
        self::strict_not_equals(false, $subject, $message); 
    }

    public static function is_null($subject, $message){ 
        self::strict_equals(null, $subject, $message); 
    }

    public static function is_not_null($subject, $message){ 
        self::strict_not_equals(null, $subject, $message); 
    }

    public static function is_array($subject, $message = null){ 
        self::is_true(is_array($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not an 'array'")); 
    }

    public static function is_bool($subject, $message = null){ 
        self::is_true(is_bool($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'bool'")); 
    }

    public static function is_callable($subject, $message = null){ 
        self::is_true(is_callable($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'callable'")); 
    }

    public static function is_double($subject, $message = null){ 
        self::is_true(is_double($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'double'")); 
    }

    public static function is_float($subject, $message = null){ 
        self::is_true(is_float($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'float'")); 
    }

    public static function is_integer($subject, $message = null){ 
        self::is_true(is_integer($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not an 'integer'")); 
    }

    public static function is_long($subject, $message = null){ 
        self::is_true(is_long($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'long'")); 
    }

    public static function is_numeric($subject, $message = null){ 
        self::is_true(is_numeric($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not 'numeric'")); 
    }

    public static function is_object($subject, $message = null){ 
        self::is_true(is_object($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not an 'object'")); 
    }

    public static function is_real($subject, $message = null){ 
        self::is_true(is_real($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'real'")); 
    }

    public static function is_resource($subject, $message = null){ 
        self::is_true(is_resource($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'resource'")); 
    }

    public static function is_scalar($subject, $message = null){ 
        self::is_true(is_scalar($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'scalar'")); 
    }

    public static function is_string($subject, $message = null){ 
        self::is_true(is_string($subject), (isset ($message) ? $message : "'" . var_export($subject, true) . "' is not a 'string'")); 
    }

    public static function key_missing($key, $array, $message = null){ 
        self::is_false(array_key_exists($key, $array), (isset ($message) ? $message : "'$key' key was present")); 
    }

    public static function key_not_missing($key, $array, $message = null){ 
        self::is_true(array_key_exists($key, $array), (isset ($message) ? $message : "'$key' key was missing")); 
    }

    public static function value_empty($subject, $message){ 
        self::is_true(empty($subject), $message); 
    }

    public static function value_not_empty($subject, $message){ 
        self::is_false(empty($subject), $message); 
    }


    public static function must($condition, $message) {
        if ( ! $condition) {
            throw new AssertException($message);
        }
    }
}

class AssertException extends \Exception {

    public function __construct($message){
        $this->message = $message;
    }

    public function __toString(){
        return basename(__CLASS__) . ': ' . $this->message;
    }
}