<?php

namespace OAuth;

use \UNBOXAPI\Box\Module;

class User extends Module {

	protected static $_name = 'OAuth';
	protected static $_models = array(
		'Users'
	);

	public static function authenticate($username,$password){
		$model = static::model(true);
		$password = \Crypt::encode($password);
		$user = $model::query()->where('username', $username )->where('password',$password)->get_one();
		$count = count($user);
		if ($count===1){
			return $user;
		}else{
			return false;
		}
	}

}