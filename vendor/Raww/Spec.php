<?php

namespace Raww;

/**
 * Spec class. Test tool.
 *
 * @package    Raww
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://rawwphp.info/license
 */
class Spec {
    
    private $description;
    private $tests;
    private $total;
    private $passed;
    private $failed;
    
    public static function describe($description) {
        return new Spec($description);
    }
    
    public function __construct($description) {
        $this->tests       = array();
        $this->description = $description;

        //events
        $this->before = function($spec){};
        $this->after  = function($spec){};
    }

    public function before($closure) {
       $this->before = $closure; 
    }

    public function after($closure) {
       $this->after = $closure; 
    }

    public function add($name, $check) {
        
        $this->tests[$name] = $check;
        return $this;
    }

    public function assert() {
        return new Assert();
    }

    public function run($renderer=null) {

        $output = array(
            'description' => $this->description,
            'duration'    => $this->description,
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
            
            $test = array($name => array(
                'passed' => 1,
                'message'=> ''
            ));

            try {
                $result = $check($this);
                $this->passed++;
            } catch (\Exception $exception) {
                $test[$name]['passed']  = 0;
                $test[$name]['message'] = (string) $exception;
                $this->failed++;
            }

            $this->total++;
            $output['tests'][] = $test;
        }

        call_user_func($this->after, $this);

        $output["duration"] = microtime() - $start;

        $output["passed"] = $this->passed;
        $output["failed"] = $this->failed;
        $output["total"]  = $this->total;

        return $renderer ? call_user_func($renderer, $output) : $output;
    }
}


class Assert {
    
    public function equals($value, $subject, $message){
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