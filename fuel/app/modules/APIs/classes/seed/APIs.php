<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/14/15
 * Time: 10:10 PM
 */

namespace Apis\seed;


class Apis extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Apis';
    protected static $_model = 'Apis';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_api',
            'name' => 'UNBOX REST Test',
            'url' => 'demo/',
            'login_required' => 0,
            'type' => 'REST',
            'deprecated' => 0,
            'version_id' => null,
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_api_login',
            'name' => 'UNBOX OAuth Test',
            'url' => '/',
            'login_required' => 1,
            'type' => 'REST',
            'deprecated' => 0,
            'version_id' => null,
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        )
    );
    protected static $_relationships = array(
        array(
            'id' => 'unbox_demo_api',
            'name' => 'applications',
            'related_id' => 'unbox_demo_app'
        ),
        array(
            'id' => 'unbox_demo_api_login',
            'name' => 'applications',
            'related_id' => 'unbox_demo_app'
        )
    );
}