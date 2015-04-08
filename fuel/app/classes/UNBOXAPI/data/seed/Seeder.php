<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/29/15
 * Time: 2:16 PM
 */

namespace UNBOXAPI\Data\Seed;


class Seeder {

    protected static $_module;
    protected static $_model;
    protected static $_records;
    protected static $_relationships;

    public static function run(){
        $module = static::$_module;
        $model = (isset(static::$_model)?"$module\\Model\\".static::$_model:"$module\\Model\\$module");

        $recordArray = array();
        foreach (static::$_records as $record => $values){
            $Record = $model::forge($values);
            $Record->save();
            $recordArray[] = $Record;
        }
    }
}