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
            'id' => 'seed_api',
            'name' => 'REST Test',
            'url' => 'test/',
            'login_required' => 0,
            'type' => 'REST',
            'deprecated' => 0,
            'version_id' => null,
            'created_by' => '1',
            'modified_by' => '1'
        )
    );
    protected static $_relationships = array(
        array(
            'id' => 'seed_api',
            'name' => 'applications',
            'related_id' => 'seed_application'
        )
    );
}