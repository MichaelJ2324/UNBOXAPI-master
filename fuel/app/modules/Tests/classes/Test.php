<?php

namespace Tests;

use \UNBOXAPI\Box\Module;

class Test extends Module {

    protected static $_canisters = array(
        'Tests',
        'Settings'
    );

    public $application_id;
    public $api_id;
    public $entrypoint_id;
    public $login_id;
    public $saved_login;
    public $web_address;

}