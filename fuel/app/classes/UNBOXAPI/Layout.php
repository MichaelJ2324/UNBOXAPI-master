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
    protected static $_enabled = false;
    protected static $_links = array();
    protected static $_templates = array();
    protected static $_options = array(
        'bootstrap' => array()
    );
    protected static $_option_overrides = array();

    public static function metadata(){
        return array(
            'name' => static::$_name,
            'label' => static::$_label,
            'label_plural' => static::$_label_plural,
            'link' => static::$_link,
            'icon' => static::$_icon,
            'enabled' => static::$_enabled,
            'links' => static::$_links,
            'templates' => static::templates(),
            'options' => static::options()
        );
    }
    protected static function options(){
        static::$_options = array_merge(self::$_options,static::$_option_overrides);
        return static::$_options;
    }
    protected static function templates(){
        $module = static::$_name;
        return \Config::load("$module::templates");
    }
}
