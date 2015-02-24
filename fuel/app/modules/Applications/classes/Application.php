<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 8:24 AM
 */

namespace Applications;


class Application extends \UNBOXAPI\Module{
    protected static $_name = "Applications";
    protected static $_label = "Application";
    protected static $_label_plural = "Applications";
    protected static $_type = "Module";
    protected static $_enabled = true;

    public static function create(){
        $application = Model\Applications::forge(\Input::json());
        $application->save();
        return $application;
    }
    public static function update($id){
        $application = Model\Applications::find($id);
        $properties = array();
        foreach(\Input::json() as $key=>$value){
            if (!($key=="id"||$key=="date_created"||$key=="date_modified")) {
                $properties[$key] = $value;
            }
        }
        $application->set($properties);
        $application->save();
        return $application;
    }
    public static function get($id=""){
        if ($id==""){
            $id='all';
            $application = Model\Applications::find($id);
            $application = static::formatResult($application);
        }else {
            $application = Model\Applications::find($id);
        }
        return $application;
    }
    public static function apis($id){
        $application = new Model\Applications();
        return $application->getAPIs($id);
    }
    public static function entryPoints($id){
        $application = new Model\Applications();
        return $application->getEntryPoints($id);
    }
} 