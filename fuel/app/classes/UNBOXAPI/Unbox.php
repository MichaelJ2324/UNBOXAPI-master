<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 9:55 AM
 */

namespace UNBOXAPI;


class Unbox {

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
                'key' => 'templates',
                'value' => \Config::load('templates')
            )
        );
        $modules = \Module::loaded();
        foreach ($modules as $module=>$path){
            if (substr($module, -1) === "s"){
                $class = substr($module,0,-1);
            }else{
                $class = $module;
            }
            $Class = "\\$module\\$class";
            $metadata[1]['value'][] = $Class::get_metaData();
        }
        return $metadata;
    }
    public static function get_config(){
        return \Config::get("unbox");
    }

    public static function installer(){
        if (\Config::get("unbox.install_locked")===false) {
            $UNBOX = new Unbox();
            $UNBOX->install();
        }else{
            throw new \Exception("Installer Locked",99);
        }
    }
    private function install(){

    }

} 