<?php

namespace UNBOXAPI\Data\Metadata;

use UNBOXAPI\Box\Factory as BoxFactory;

class Manager {

	public $metadata = array();

	protected static $_instance;
	protected static $_config;
	protected static $_caching;
	protected static $_cache_name;
	protected static $_logged_in;
	protected static $_lang;

	protected static function setupConfig(){
		if (empty(static::$_config)) {
			$config = \Config::load("meta");
			\Log::debug(print_r($config,true));
			if ($config == FALSE) {
				$config = \Config::get("meta");
			}
			static::$_config = $config;
		}
		return static::$_config;
	}
	protected static function setupCaching(){
		static::$_caching = static::$_config['caching'];
		static::$_cache_name = static::$_config['cache_name'];
	}
	public static function loggedIn($loggedIn = null){
		if ($loggedIn !== null) {
			static::$_logged_in = (intval($loggedIn)===1?true:false);
		}
		return static::$_logged_in;
	}
	public static function getInstance()
	{
		if (empty($_instance)) {
			$_instance = new static();
		}
		return $_instance;
	}
	public static function cacheEnabled(){
		return static::$_caching;
	}

	protected function __construct(){
		static::setupConfig();
		static::setupCaching();
	}
	private function __clone()
	{
	}
	private function __wakeup()
	{
	}
	public function getProperty(){

	}
	public function setProperty(){

	}

	public static function systemMetadata(){
		$MetadataManager = self::getInstance();
		if (static::cacheEnabled()) {
			try {
				$cache                     = static::$_cache_name.".system".(static::$_logged_in?'-auth':'-no-auth');
				$MetadataManager->metadata = \Cache::get($cache);
			} catch (\CacheNotFoundException $e) {
				\Log::error($e->getMessage());
			}
		}
		if (empty($MetadataManager->metadata)){
			$MetadataManager->metadata = static::$_config['available_properties']['System'];
			$MetadataManager->metadata['config'] = static::config("System");
			$MetadataManager->metadata['labels'] = static::labels("System");
			$MetadataManager->metadata['templates'] = static::templates("System");
			$MetadataManager->metadata['modules'] = array();
			$MetadataManager->metadata['layouts'] = array();
			$modules = \Module::loaded();
			foreach ($modules as $box_name=>$path){
				if (!in_array(strtolower($box_name),static::$_config['excluded_modules'])) {
					$boxMeta = static::boxMetadata($box_name);
					$type = BoxFactory::type($box_name);
					if ($type === 'Module') {
						$ModuleMetaConfig = static::getBoxMetaConfig($type);
						foreach($ModuleMetaConfig as $meta => $properties){
							if ($properties['export'] == false){
								unset($boxMeta[$meta]);
							}
						}
						//if (($boxMeta['config']['login']===true && static::$_logged_in===true) || ($boxMeta['config']['login']===false)) {
							$boxMeta['name'] = $box_name;
							$MetadataManager->metadata['modules'][] = $boxMeta;
						//}
					} else if ($type === 'Layout') {
						$LayoutMetaConfig = static::getBoxMetaConfig($type);
						foreach($LayoutMetaConfig as $meta => $properties){
							if ($properties['export'] == false){
								unset($boxMeta[$meta]);
							}
						}
						//if (($boxMeta['config']['login']===true && static::$_logged_in===true) || ($boxMeta['config']['login']===false)) {
							$boxMeta['name'] = $box_name;
							$MetadataManager->metadata['layouts'][] = $boxMeta;
						//}
					}
				}
			}
			if (static::cacheEnabled()){
				\Cache::set($cache,$MetadataManager->metadata);
			}
		}
		return $MetadataManager->metadata;

	}
	protected static function sysConfig($all = false){
		$config = \Config::get("unbox");
		if (!$all){
			unset($config['oauth']);
			unset($config['google']);
		}
		return $config;
	}

	public static function boxMetadata($box_name) {
		if (static::cacheEnabled()){
			try{
				$cache_name = static::$_cache_name.".modules.".$box_name;
				$box_meta = \Cache::get($cache_name);
			}catch(\CacheNotFoundException $e){
				\Log::error("Metadata cache for $box_name not found.");
			}
		}
		if (empty($box_meta)) {
			$box_type = BoxFactory::type($box_name);
			$box_meta = static::getBoxMetaConfig($box_type);
			if ($box_meta !== FALSE && is_array($box_meta)) {
				foreach ($box_meta as $meta_key => $properties) {
					\Log::debug("Building $meta_key Metadata for $box_name");
					$box_meta[$meta_key] = static::$meta_key($box_name);
				}
				if (static::cacheEnabled()) {
					\Cache::set($cache_name, $box_meta);
				}
			}
		}
		return $box_meta;
	}
	protected static function getBoxMetaConfig($box_type){
		return static::$_config['available_properties'][$box_type];
	}
	/**
	 * Gets the config for the Module
	 * @return array
	 */
	public static function config($box_name){
		if (strtolower($box_name) !== 'system'){
			if (\Module::exists($box_name)) {
				$config = \Config::load("$box_name::module");
				if ($config == FALSE) {
					$config = \Config::get("$box_name::module");
				}
				return $config;
			}else{
				\Log::error("Cannot get Config for $box_name. Box does not exist.");
			}
		}else{
			return static::sysConfig();
		}
	}

	public static function labels($box_name){
		$lang = \Lang::get_lang();
		if (strtolower($box_name) !== 'system'){
			if (\Module::exists($box_name)){
				$langDefs = \Config::load(APPPATH."modules\\$box_name\\lang\\$lang\\labels.php");
				if ($langDefs == FALSE) {
					$langDefs = \Config::get(APPPATH."modules\\$box_name\\lang\\$lang\\labels.php");
				}
				return $langDefs;
			}
		}else{
			$langDefs = \Config::load(APPPATH."lang\\$lang\\labels.php");
			if ($langDefs == FALSE) {
				$langDefs = \Config::get(APPPATH."lang\\$lang\\labels.php");
			}
			return $langDefs;
		}
	}
	//Module specific ones
	/**
	 * Gets the seeders configured for the module
	 * @return mixed - False if no seeders configured, Array of seeders otherwise
	 */
	public static function seeds($box_name)
	{
		if (\Module::exists($box_name)) {
			$seeds = \Config::load("$box_name::seed");
			if ($seeds == FALSE) {
				$seeds = \Config::get("$box_name::seed");
			}
			return $seeds;
		}else{
			\Log::error("Cannot get Seeds for $box_name. Box does not exist.");
		}
	}

	/**
	 * Get the fields configured on the module
	 * @return array
	 */
	public static function fields($box_name,$model=TRUE){
		if (\Module::exists($box_name)) {
			$Class  = BoxFactory::classify($box_name);
			$model  = $Class::model($model);
			$fields = $model::fields();
			return $fields;
		}else{
			\Log::error("Cannot get Fields for $box_name. Box does not exist.");
		}
	}
	/**
	 * Get the relationships configured on the module
	 * @return array
	 */
	public static function relationships($box_name,$model=TRUE){
		if (\Module::exists($box_name)) {
			$Class         = BoxFactory::classify($box_name);
			$model         = $Class::model($model);
			$relationships = $model::relationships();
			return $relationships;
		}else{
			\Log::error("Cannot get Relationships for $box_name. Box does not exist.");
		}
	}
	public static function views($box_name){
		if (\Module::exists($box_name)) {
			$views = \Config::load("$box_name::views");
			if ($views == FALSE) {
				$views = \Config::get("$box_name::views");
			}
			return $views;
		}else{
			\Log::error("Cannot get Views for $box_name. Box does not exist.");
		}
	}
	public static function canisters($box_name){
		if (\Module::exists($box_name)) {
			$Class         = BoxFactory::classify($box_name);
			$canisters    = $Class::canisters();
			return $canisters;
		}else{
			\Log::error("Cannot get Canisters for $box_name. Box does not exist.");
		}
	}
	//Layout specific ones
	public static function templates($box_name){
		if (strtolower($box_name) !== 'system') {
			if (\Module::exists($box_name)) {
				$templates = \Config::load("$box_name::templates");
				if ($templates == FALSE) {
					$templates = \Config::get("$box_name::templates");
				}
				return $templates;
			}else{
				\Log::error("Cannot get Templates for $box_name. Box does not exist.");
			}
		}else{
			$templates = \Config::load('templates');
			if ($templates==FALSE){
				$templates = \Config::get('templates');
			}
			return $templates;
		}
	}
	public static function links($box_name){
		if (\Module::exists($box_name)) {
			$links = \Config::load("$box_name::links");
			if ($links == FALSE) {
				$links = \Config::get("$box_name::links");
			}
			return $links;
		}else{
			\Log::error("Cannot get Links for $box_name. Box does not exist.");
		}
	}
}