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
    protected static $_model = 'Users';

    protected static $_records = array(
        array(
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'username' => 'admin',
            'password' => 'asdf',
            'email' => 'email@example.com'
        )
    );
    public static function run(){
        $records = array();
        foreach(static::$_records as $record){
            $record['password'] = \Crypt::encode($record['password']);
            $records[] = $record;
        }
        static::$_records = $records;
        parent::run();
    }
}