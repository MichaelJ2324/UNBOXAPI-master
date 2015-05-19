<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 12:19 AM
 */

namespace Entrypoints\seed;


class Entrypoints extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Entrypoints';
    protected static $_model = 'Entrypoints';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_ep1',
            'method' => 1,
            'url' => 'HelloWorld/',
            'description' => '',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Hello World',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_ep2',
            'method' => 2,
            'url' => 'Ping/',
            'description' => '',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Ping',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth1',
            'method' => 2,
            'url' => 'oauth/token',
            'description' => 'Request an OAuth2.0 Token',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'OAuth Token',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth2',
            'method' => 2,
            'url' => 'oauth/refresh',
            'description' => 'Refreshes OAuth token',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'OAuth Refresh',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth3',
            'method' => 1,
            'url' => 'oauth/me',
            'description' => 'Validates token is good, and provides token user info.',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'User',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth4',
            'method' => 1,
            'url' => 'oauth/revoke',
            'description' => 'Revokes access token, so client is logged out.',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'User',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        )
    );
    protected static $_relationships = array(
        array(
            'id' => 'unbox_demo_ep1',
            'name' => 'apis',
            'related_id' => 'unbox_demo_api'
        ),
        array(
            'id' => 'unbox_demo_ep2',
            'name' => 'apis',
            'related_id' => 'unbox_demo_api'
        ),
        array(
            'id' => 'unbox_demo_oauth1',
            'name' => 'apis',
            'related_id' => 'unbox_demo_api_login'
        ),
        array(
            'id' => 'unbox_demo_oauth2',
            'name' => 'apis',
            'related_id' => 'unbox_demo_api_login'
        ),
        array(
            'id' => 'unbox_demo_oauth3',
            'name' => 'apis',
            'related_id' => 'unbox_demo_api_login'
        )
    );
}