<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/14/15
 * Time: 10:10 PM
 */

namespace APIs\seed;


class APIs extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'APIs';
    protected static $_model = 'APIs';

    protected static $_records = array(
        array(
            'name' => 'REST Test',
            'url' => 'rest/v10/',
            'login_required' => 1,
            'type' => 'REST',
            'deprecated' => 0,
            'version_id' => null
        )
    );

}