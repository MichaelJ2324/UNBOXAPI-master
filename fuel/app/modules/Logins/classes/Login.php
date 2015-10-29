<?php

namespace Logins;

use UNBOXAPI\Box\Module;

class Login extends Module{

    protected static $_canisters = array(
        'Logins',
        'Settings'
    );

    public $deprecated;
    public $login_entrypoint_id;
    public $logout_entrypoint_id;

} 