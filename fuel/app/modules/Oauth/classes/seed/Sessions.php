<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/30/15
 * Time: 7:09 AM
 */

namespace Oauth\Seed;

//Deprecated
class Sessions extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Oauth';
    protected static $_model = 'Sessions';

    protected static $_records = array(
        array(
            'owner_type' => 'client',
            'owner_id' => '',
            'client_id' => '',
            'client_redirect_uri' => null
        )
    );

    protected static function records(){
        $records = array();
        foreach(static::$_records as $record => $values){
            $values['owner_id'] = \Config::get('unbox.oauth.client.id');
            $client = \Oauth\Model\Clients::query()->where('client_id',\Config::get('unbox.oauth.client.id'))->get_one();
            $values['client_id'] = $client->id;
            $records[] = $values;
        }
        static::$_records = $records;
        return parent::records();
    }

}