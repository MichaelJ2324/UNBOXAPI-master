<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/30/15
 * Time: 7:09 AM
 */

namespace Oauth\Seed;


class Scopes extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Oauth';
    protected static $_model = 'Scopes';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_scope',
            'scope' => 'demo_app',
            'description' => 'Access to Demo API only.',
        ),
        array(
            'scope' => 'profile',
            'description' => 'Access to profile information on account.',
        ),
        array(
            'scope' => 'client',
            'description' => 'Able to access all resources. For VPS servers',
        )
    );

}