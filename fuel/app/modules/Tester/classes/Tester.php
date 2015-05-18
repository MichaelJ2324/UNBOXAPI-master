<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/3/14
 * Time: 10:08 AM
 */

namespace Tester;


class Tester extends \UNBOXAPI\Layout{

    protected static $_name = "Tester";
    protected static $_label = "Tester";
    protected static $_label_plural = "Tester";
    protected static $_link = "#test";
    protected static $_icon = "<i class='fa fa-check-square'></i>";
    protected static $_links = array(
        'tutorial' => array(
            'name' => 'Save',
            'link' => "#save",
            'icon' => "",
            'type' => "",
            'layout' => "",
            'enabled' => true
        )
    );

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
        //TODO::Add logic for running a Test using the Request/Rest object
        return $response;
    }
} 