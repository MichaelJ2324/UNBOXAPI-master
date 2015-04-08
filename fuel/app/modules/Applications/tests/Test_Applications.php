<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/12/15
 * Time: 2:06 AM
 */

class Test_Applications extends TestCase{

    public function test_insert(){
        $application = new \Applications\Application();
        $application->name = "Test";
        $application->description = "Stuff";
        $application = \Applications\Application::create($application);
        print_r($application);
        $this->assertTrue(true);
    }
    public function test_select(){
        $id = "";
        $application = \Applications\Application::get($id);
        print_r($application);
        $this->assertTrue(true);
    }
}