<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/12/15
 * Time: 2:06 AM
 */

class Test_Application extends TestCase{

    public function test_insert(){
        $application = new \Applications\Application();
        $application->id = "12345";
        $application->name = "Test";
        $application->description = "Stuff";
        $application->save();
        $this->assertTrue(true);
    }
    public function test_select(){
        $id = "";
        $application = \Applications\Application::get($id);
        $this->assertTrue(true);
    }
}