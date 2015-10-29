<?php

namespace Tester;

use \UNBOXAPI\Box\Layout;

class Tester extends Layout{

    public static function get($test_id){
        //TODO::Add logic for getting the Test object for Public facing Tests
        $model = \Tests\Test::get($test_id);
        return $model;
    }
    public static function run($test_id=""){
        $response = array();
        if ($test_id===""){
            $Test = new \Tests\Test();
            foreach(\Input::json() as $key => $value){
                $Test->{$key} = $value;
            }
        }else{
            $Test = new \Tests\Test();
            $Test->load($test_id);
        }
        //TODO::Add logic for running a Test using the Fuel \Request Object
        return $response;
    }
} 