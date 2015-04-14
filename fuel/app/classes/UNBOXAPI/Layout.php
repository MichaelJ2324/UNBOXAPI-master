<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/9/15
 * Time: 5:32 PM
 */

namespace UNBOXAPI;


class Layout {
    protected static $_name = "";
    protected static $_label = "";
    protected static $_label_plural = "";
    protected static $_link = "";
    protected static $_icon = "";
    protected static $_links = array();
    protected static $_templates;
    protected static $_config;

    public static function metadata(){
        return array(
            'name' => static::$_name,
            'label' => static::$_label,
            'label_plural' => static::$_label_plural,
            'link' => static::$_link,
            'icon' => static::$_icon,
            'links' => static::$_links,
            'templates' => static::templates(),
            'config' => static::config()
        );
    }
    protected static function config(){
        static::$_config = \Config::load(static::$_name."::module");
        return static::$_config;
    }
    protected static function templates(){
        $module = static::$_name;
        static::$_templates = \Config::load("$module::templates");
        return static::$_templates;
    }
}
