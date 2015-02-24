<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/4/14
 * Time: 12:56 PM
 */

namespace UNBOXAPI;


class Module {
    protected static $_name = "";
    protected static $_label = "";
    protected static $_label_plural = "";
    protected static $_link = "";
    protected static $_type = "";
    protected static $_icon = "";
    protected static $_enabled = false;
    protected static $_fields = array();
    protected static $_links = array();
    protected static $_templates = array();
    protected static $_options = array();

    public static function get_metaData(){
        if (static::$_type=="Layout"){
            return array(
                'name' => static::$_name,
                'label' => static::$_label,
                'link' => static::$_link,
                'type' => static::$_type,
                'icon' => static::$_icon,
                'enabled' => static::$_enabled,
                'links' => static::$_links,
                'fields' => null,
                'relationships' => null,
                'templates' => static::get_templates(),
                'options' => static::$_options
            );
        }else{
            return array(
                'name' => static::$_name,
                'label' => static::$_label,
                'link' => null,
                'type' => static::$_type,
                'icon' => static::$_icon,
                'enabled' => static::$_enabled,
                'links' => null,
                'fields' => static::get_fields(),
                'relationships' => static::get_relationships(),
                'templates' => null,
                'options' => static::$_options
            );
        }
    }
    public static function get_fields(){
        if (static::$_type==='Module') {
            $module = static::$_name;
            $model = "$module\\Model\\$module";
            return $model::fields();
        }else{
            return false;
        }
    }
    public static function get_templates(){
        if (static::$_type==='Layout') {
            $module = static::$_name;
            $module = "$module\\$module";
            return $module::templates();
        }else{
            return false;
        }
    }
    public static function get_relationships(){
        if (static::$_type==='Module') {
            $module = static::$_name;
            $model = "$module\\Model\\$module";
            return $model::relationships();
        }else{
            return false;
        }
    }
    protected static function templates(){
        if (static::$_type==='Layout'){
            $module = static::$_name;
            return \Config::load("$module::templates");
        }else{
            return false;
        }
    }
    protected static function formatResult($model){
        $rows = array();
        foreach ($model as $i => $obj) {
            $rows[] = $obj->to_array();
        }
        return $rows;
    }
} 