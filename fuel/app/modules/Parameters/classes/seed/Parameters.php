<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 12:24 AM
 */

namespace Parameters\seed;


class Parameters extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Parameters';
    protected static $_model = 'Parameters';

    protected static $_records = array(
        array(
            'data_type' => 'integer',
            'api_type' => 'record',
            'description' => '',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Get Pong'
        )
    );

}