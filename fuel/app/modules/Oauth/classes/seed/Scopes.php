<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/30/15
 * Time: 7:09 AM
 */

namespace Oauth\Seed;


class Scopes extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'OAuth';
    protected static $_model = 'Scopes';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_scope',
            'scope' => 'demo_api',
            'description' => 'Access to Demo API only',
        ),
        array(
            'scope' => 'profile',
            'description' => 'Access to user profile information.',
        ),
        array(
            'scope' => 'client',
            'description' => 'Able to create users and user oauth clients, used by Application Deployments.',
        ),
		array(
			'scope' => 'admin',
			'description' => 'Able to create users and user oauth clients, used by Application Deployments.',
		),
		array(
			'scope' => 'api',
			'description' => 'Access to all Rest API.',
		),
    );

}