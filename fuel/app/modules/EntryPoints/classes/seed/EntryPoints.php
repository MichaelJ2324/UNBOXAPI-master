<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 12:19 AM
 */

namespace EntryPoints\seed;


class EntryPoints extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'EntryPoints';
    protected static $_model = 'EntryPoints';

    protected static $_records = array(
        array(
            'id' => 'seed_ep_1',
            'method' => 1,
            'url' => 'HelloWorld/',
            'description' => '',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Hello World',
            'created_by' => '1',
            'modified_by' => '1'
        ),
        array(
            'id' => 'seed_ep_2',
            'method' => 2,
            'url' => 'Ping/',
            'description' => '',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Ping',
            'created_by' => '1',
            'modified_by' => '1'
        )
    );
    protected static $_relationships = array(
        array(
            'id' => 'seed_ep_1',
            'name' => 'apis',
            'related_id' => 'seed_api'
        ),
        array(
            'id' => 'seed_ep_2',
            'name' => 'apis',
            'related_id' => 'seed_api'
        )
    );
}