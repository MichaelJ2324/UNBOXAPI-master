<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 9:03 AM
 */

namespace Logins;

class Login extends \UNBOXAPI\Module{

    protected static $_name = "Logins";
    protected static $_label = "Login";
    protected static $_label_plural = "Logins";

    protected static $_models = array(
        'Logins',
        'Settings'
    );

    public $deprecated;
    public $login_entrypoint_id;
    public $logout_entrypoint_id;

} 