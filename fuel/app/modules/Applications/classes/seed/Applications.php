<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/14/15
 * Time: 10:09 PM
 */

namespace Applications\Seed;


class Applications extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Applications';
    protected static $_model = 'Applications';
    protected static $_records = array(
        array(
            'id' => 'unbox_demo_app',
            'description' => 'UNBOX Demo Application for testing.',
            'version_id' => null,
            'name' => 'UNBOX API Demo',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        )
    );
}