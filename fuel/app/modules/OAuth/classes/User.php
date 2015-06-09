<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/31/15
 * Time: 9:10 PM
 */

namespace OAuth;


class User extends \UNBOXAPI\Module {

	protected static $_name = 'OAuth';
	protected static $_models = array(
		'Users'
	);

	public static function authenticate($username,$password){
		\Log::debug($password);
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