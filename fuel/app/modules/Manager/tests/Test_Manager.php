<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 12/15/14
 * Time: 2:52 PM
 */

use Manager\Manager;

class Test_Manager extends TestCase {
    public $templates = array(
        'test' => "<div>Text</div>"
    );
    public function test_templates(){
        $templates = Manager::get_templates();
        $templates = serialize($templates);
        $this->assertEquals(serialize($this->templates),$templates);
    }
}