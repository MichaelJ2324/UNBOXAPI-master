<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 4/3/15
 * Time: 5:30 PM
 */

namespace ParameterTypes\Seed;


use UNBOXAPI\Data\Seed\Seeder;

class BaseTypes extends Seeder
{
    protected static $_module = 'ParameterTypes';

    public static function run(){
        $config = \Config::load(static::$_module."::baseTypes");
        $dataTypes = $config['data_types'];
        $apiTypes = $config['api_types'];
        $records = array();
        foreach($dataTypes as $key => $type){
            $records[] = array(
                'name' => $type,
                'type' => 1,
                'template' => $config['templates'][$type]
            );
        }
        foreach($apiTypes as $key => $type){
            $records[] = array(
                'name' => $type,
                'type' => 2,
                'template' => $config['templates'][$type]
            );
        }
        static::$_records = $records;
    }

}