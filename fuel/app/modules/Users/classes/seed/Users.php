<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 8:19 AM
 */

namespace Users\Seed;


class Users extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Users';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_user',
            'first_name' => 'Unbox',
            'last_name' => 'Demo',
            'username' => 'unbox_demo',
            'password' => 'unbox',
            'email' => 'demo@unboxapi.com'
        )
    );
    protected static function records(){
        $records = array();
        foreach(static::$_records as $record=>$values){
            $values['password'] = \Crypt::encode($values['password']);
            $records[] = $values;
        }
        static::$_records = $records;
        return static::$_records;
    }
}