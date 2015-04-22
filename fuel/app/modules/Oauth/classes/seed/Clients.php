<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/30/15
 * Time: 6:39 AM
 */

namespace Oauth\Seed;


class Clients extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Oauth';
    protected static $_model = 'Clients';

    protected static $_records = array(
        array(
            'client_id' => '',
            'secret' => '',
            'name' => ''
        )
    );

    protected static function records(){
        $records = array();
        foreach(static::$_records as $record => $values){
            $values['client_id'] = \Config::get('unbox.oauth.client.id');
            $values['secret'] = \Config::get('unbox.oauth.client.secret');
            $values['name'] = \Config::get('unbox.oauth.client.name');
            $records[] = $values;
        }
        static::$_records = $records;
        return parent::records();
    }

}