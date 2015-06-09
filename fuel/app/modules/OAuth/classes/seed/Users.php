<?php

namespace OAuth\Seed;


class Users extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'OAuth';
	protected static $_model = 'Users';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_user',
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