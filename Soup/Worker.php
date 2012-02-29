<?php

namespace Worker;

/**
 * Worker class.
 *
 * @package    Soup
 * @author     Artur Heinze
 * @copyright  (c) since 2011 d-xp.com
 * @license    http://Soupphp.info/license
 */
class Worker {
    
    public $description;
    protected $log;
	
    public function __construct() {
    
    }
	

    public function before() {
        
    }

    public function after($output) {
        
    }
	
	public function finally($output) {
        
    }
	
	public function clearlog(){
		$this->log = array();
	}
	
	public function getLog(){
		return $this->log;
	}

	protected function log($message){
		$this->log[] = $message;
	}

    public function run($renderer=null) {
		
		$classname = get_called_class();
		
		$obj     = new $classname();
		$methods = array();
		
		foreach(get_class_methods($obj) as $method) {
		  if (substr($method, 0,4)=="task") {
            $methods[$method] = array($obj, $method);    
          }
		}
        
        ksort($methods);
		
        $output = array(
            'description' => $obj->description,
            'tasks'       => array_keys($methods),
			'logs'		  => array(),
            'completed'   => true,
            'duration'    => 0,
            'tasks_completed' => array(),
        );

        $start = microtime();
        
        $obj->before();

        foreach ($methods as $name => $callback) {
            
			$obj->clearlog();
			
            $result = call_user_func($callback);
			
			$output['logs'][$name] = $obj->getlog();
			
            if ($result===false) {
                $output['completed']  = false;
                $output['stopped_at'] = $name;
                break;   
            } else {
                $output['tasks_completed'][] = $name;
            }
        }

        $output["duration"] = microtime() - $start;

        if(!$output['completed']) {
            $obj->after($output);
        }

        $obj->finally($output);

        return $renderer ? call_user_func($renderer, $output) : $output;
    }
}