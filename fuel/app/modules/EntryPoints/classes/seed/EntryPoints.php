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
            'method' => 1,
            'url' => 'HelloWorld/',
            'description' => '',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Hello World'
        ),
        array(
            'method' => 2,
            'url' => 'Ping/',
            'description' => '',
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Ping'
        )
    );

}