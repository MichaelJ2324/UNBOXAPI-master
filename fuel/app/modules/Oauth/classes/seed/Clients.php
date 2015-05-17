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
            'client_id' => 'unbox_demo_api_client',
            'secret' => 'unbox_demo_api_secret',
            'name' => 'UNBOX Demo Client'
        )
    );

    protected static function records(){
        $record = array(
            'client_id' => \Config::get('unbox.oauth.client.id'),
            'secret' => \Config::get('unbox.oauth.client.secret'),
            'name' => \Config::get('unbox.oauth.client.name')
        );
        static::$_records[] = $record;
        return static::$_records;
    }

}