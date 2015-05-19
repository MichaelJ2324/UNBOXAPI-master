<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/7/15
 * Time: 9:15 AM
 */

namespace Logins\Seed;


class Logins extends \UNBOXAPI\Data\Seed\Seeder {

    protected static $_module = 'Logins';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_oauth_login',
            'login_entrypoint_id' => 'unbox_demo_oauth1',
            'logout_entrypoint_id' => 'unbox_demo_oauth4',
            'name' => 'OAuth 2.0',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
    );
    protected static $_relationships = array(
        array(
            'id' => 'unbox_demo_oauth_login',
            'name' => 'apis',
            'related_id' => 'unbox_demo_api_login',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user'
            )
        ),
    );

}