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
            'id' => 'seed_application',
            'description' => 'Seeded application for testing.',
            'version_id' => null,
            'name' => 'UNBOX API Test',
            'created_by' => '1',
            'modified_by' => '1'
        )
    );
}