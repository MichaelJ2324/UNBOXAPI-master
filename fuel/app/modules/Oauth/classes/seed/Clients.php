<?php

namespace OAuth\Seed;


class Clients extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Oauth';
    protected static $_model = 'Clients';

    protected static $_records = array(
        array(
            'client_id' => 'unbox_demo_api_client',
            'secret' => 'unbox_demo_api_secret',
            'name' => 'UNBOX Demo Client',
			'type' => 'api_user'
        )
    );

    protected static function records(){
        $record = array(
            'client_id' => \Config::get('unbox.oauth.client.id'),
            'secret' => \Config::get('unbox.oauth.client.secret'),
            'name' => \Config::get('unbox.oauth.client.name'),
			'type' => 'unbox_client'
        );
        static::$_records[] = $record;
        return static::$_records;
    }

}