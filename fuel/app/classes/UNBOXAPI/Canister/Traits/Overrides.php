<?php

namespace UNBOXAPI\Canister\Traits;

trait Overrides {

	protected static $_connection = 'default';
	protected static $_fields = array();
	protected static $_properties = array();
	protected static $_default_fields = array();
	protected static $_relationships = array(
		'belongs_to' => array(),
		'has_one' => array(),
		'has_many' => array(),
		'many_many' => array()
	);
	protected static $_belongs_to = array();
	protected static $_has_one = array();
	protected static $_has_many = array();
	protected static $_many_many = array();

	public static function properties(){
		self::$_properties = array_merge(static::$_default_fields, static::$_fields);
		return parent::properties();
	}
	public static function relations($specific = false){
		if (isset(static::$_relationships['belongs_to'])) {
			self::$_belongs_to = array_merge(self::$_relationships['belongs_to'],static::$_relationships['belongs_to']);
		}else{
			self::$_belongs_to = self::$_relationships['belongs_to'];
		}
		if (isset(static::$_relationships['has_one'])) {
			self::$_has_one = array_merge(self::$_relationships['has_one'],static::$_relationships['has_one']);
		}else{
			self::$_has_one = self::$_relationships['has_one'];
		}
		if (isset(static::$_relationships['has_many'])) {
			self::$_has_many = array_merge(self::$_relationships['has_many'],static::$_relationships['has_many']);
		}else{
			self::$_has_many = self::$_relationships['has_many'];
		}
		if (isset(static::$_relationships['many_many'])) {
			self::$_many_many = array_merge(self::$_relationships['many_many'],static::$_relationships['many_many']);
		}else{
			self::$_many_many = self::$_relationships['many_many'];
		}
		return parent::relations($specific);
	}

	public static function fields(){
		$properties = static::properties();
		$fields = array();
		foreach ($properties as $field=>$attributes){
			$fields[$field] = array(
				'data_type' => $attributes['data_type'],
				'label' => $attributes['label'],
				'required' => isset($attributes['validation']['required'])?$attributes['validation']['required']:false,
				'validation' => isset($attributes['validation'])?$attributes['validation']:array(),
				'form' => isset($attributes['form'])?$attributes['form']:array(),
				'filter' => isset($attributes['filter'])?$attributes['filter']:false,
			);
		}
		return $fields;
	}
	public static function relationships(){
		$relations = static::relations();
		$relationships = array();
		foreach ($relations as $relationshipName=>$relationshipObject){
			if (strpos(get_class($relationshipObject),"ManyMany")) {
				$type = "ManyMany";
			}else if (strpos(get_class($relationshipObject),"HasMany")) {
				$type = "HasMany";
			}else if (strpos(get_class($relationshipObject),"HasOne")) {
				$type = "HasOne";
			}else if (strpos(get_class($relationshipObject),"BelongsTo")) {
				$type = "BelongsTo";
			}
			$canister = $relationshipObject->__get("model_to");
			$arr = explode("\\",$canister);
			$box = $arr[0];
			$canister = end($arr);
			$relationships[$relationshipName] = array(
				'type' => $type,
				'box' => $box,
				'canister' => $canister
			);
			unset($arr);
		}
		return $relationships;
	}

}