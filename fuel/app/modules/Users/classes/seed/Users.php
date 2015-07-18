<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 8:19 AM
 */

namespace Users\Seed;


class Users extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Users';
	protected static $_eav = true;

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_user',
            'first_name' => 'Unbox',
            'last_name' => 'Demo',
            'username' => 'unbox_demo',
            'primary_email' => 'demo@unboxapi.com'
        )
    );
}