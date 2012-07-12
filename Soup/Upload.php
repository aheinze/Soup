<?php

namespace Soup;

class Upload {

	protected $name;
	protected $exists;
	protected $is_multiple;
	protected $allowed;
	protected $files = array();

	public $max_size;
	public $mime_types;

	public function __construct($name='', $settings=array()){
		
		foreach ($settings as $key => $value) {
			$this->{$key} = $value;
		}		

		$this->name = $name;
		$this->exists = isset($_FILES[$name]);

		if(!$this->exists){
			return;
		}

		$this->is_multiple = is_array($_FILES[$name]["name"]);

		if($this->is_multiple){

			foreach ($_FILES[$name]["name"] as $index => $n) {
				$file = array();
				foreach ($_FILES[$name] as $key => $value) {
					$file[$key] = $_FILES[$name][$key][$index];
				}
				$this->files[] = $file;
			}

		}else{
			$file = array();
			foreach ($_FILES[$name] as $key => $value) {
				$file[$key] = $value;
			}
			$this->files[] = $file;
		}
	}

	public function is_valid(){

		if(!$this->exists){
			return false;
		}

		if($this->max_size){
			foreach ($this->files as $file) {
				if($file["size"] > $this->max_size){
					return false;
				}
			}
		}

		if($this->mime_types){
			
			$finfo = finfo_open(FILEINFO_MIME_TYPE);

			foreach ($this->files as $file) {
				
				$type = finfo_file($finfo, $file["tmp_name"]);

				if(!in_array($type, $this->mime_types)){
					return false;
				}
			}
		}

		return true;
	}

	public function move($target = "/"){

		if(is_callable($target)){
			foreach ($this->files as $file) {
				$target($file);
			}

			return true;

		}elseif (is_string($target) && file_exists($target)) {
			foreach ($this->files as $file) {
				copy($file["tmp_name"], rtrim($target,"\\/")."/".$file["name"]);
			}

			return true;
		}

		return false;
	}
}