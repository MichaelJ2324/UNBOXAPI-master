<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 9:55 AM
 */

namespace UNBOXAPI;


class Metadata {

    protected static $_excluded_modules = array(
        'Oauth',
        'Users'
    );

    public static function get_metaData(){
        $metadata = array(
            array(
                'key' => 'config',
                'value' => self::get_config(),
            ),
            array(
                'key' => 'modules',
                'value' => array(),
            ),
            array(
                'key' => 'layouts',
                'value' => array(),
            ),
            array(
                'key' => 'templates',
                'value' => \Config::load('templates')
            )
        );
        $modules = \Module::loaded();
        foreach ($modules as $module=>$path){
            if (!in_array($module,static::$_excluded_modules)) {
                if (substr($module, -1) === "s") {
                    $class = substr($module, 0, -1);
                } else {
                    $class = $module;
                }
                $Class = "\\$module\\$class";
                $moduleMeta = $Class::metadata();
                if (get_parent_class($Class) == 'UNBOXAPI\Module') {
                    if ($moduleMeta['enabled'] == true) $metadata[1]['value'][] = $moduleMeta;
                } else if (get_parent_class($Class) == 'UNBOXAPI\Layout') {
                    $metadata[2]['value'][] = $moduleMeta;
                }
                unset($object);
            }
        }
        return $metadata;
    }
    public static function get_config(){
        return \Config::get("unbox");
    }

    public function install($config){
        try {
            if ($config['locked']===false){
                if ($this->configure_database($config['database'])){

                }
            }else{
                throw new \Exception("Installer is locked. Please set the 'locked' property of the install configuration to false and try again.");
            }
        }catch(\Exception $ex){
            return $ex->getMessage();
        }

    }

} 