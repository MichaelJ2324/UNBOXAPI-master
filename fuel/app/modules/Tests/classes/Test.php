<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/11/15
 * Time: 12:39 AM
 */

namespace Tests;


class Test extends \UNBOXAPI\Module {

    protected static $_name = "Tests";
    protected static $_label = "Test";
    protected static $_label_plural = "Tests";

    protected static $_models = array(
        'Tests',
        'Settings'
    );

    public $application_id;
    public $api_id;
    public $entryPoint_id;
    public $login_id;
    public $saved_login;
    public $web_address;

}