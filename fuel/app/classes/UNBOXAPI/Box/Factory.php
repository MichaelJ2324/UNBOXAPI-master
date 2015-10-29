<?php

namespace UNBOXAPI\Box;

class Factory {

	public static function classify($module,$box_name = ""){
		if ($box_name === ""){
			if (substr($module, -1) === "s"){
				$box = substr($module,0,-1);
			}else{
				$box = $module;
			}
		}else{
			$box = $box_name;
		}
		$class = "\\$module\\$box";
		if (class_exists($class)){
			return $class;
		}else{
			\Log::error("Box ($class) doesn't exist.");
			return false;
		}
	}
	public static function modulify($class_name){
		$name_array = explode("\\",$class_name);
		if (\Module::exists($name_array[0])){
			return $name_array[0];
		}else{
			\Log::error("Module ({$name_array[0]}) doesn't exist.");
			return false;
		}
	}
	public static function type($box){
		if (!is_object($box)){
			$box = static::classify($box);
		}
		$box = get_parent_class($box);
		if (strpos($box,"Module")!==FALSE){
			return "Module";
		}else if (strpos($box,"Layout")!==FALSE){
			return "Layout";
		}else{
			\Log::error("Invalid box object provided.");
			return false;
		}
	}

	public static function build($box_name,$args = array()){
		if (is_array($box_name)){
			$box_class = static::classify($box_name[0],$box_name[1]);
		}else{
			$box_class = static::classify($box_name);
		}
		return new $box_class($args);
	}
}