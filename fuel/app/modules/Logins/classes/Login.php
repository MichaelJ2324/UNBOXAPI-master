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
    protected static $_enabled = true;

    public $deprecated;
    public $login_entryPoint_id;
    public $logout_entryPoint_id;

} 