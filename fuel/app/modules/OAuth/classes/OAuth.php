<?php

namespace OAuth;

use \UNBOXAPI\Box\Module;

class OAuth extends Module {

	protected static $_canisters = array(
		'AccessTokens',
		'AuthCodes',
		'Clients',
		'RedirectUris',
		'RefreshTokens',
		'Scopes',
		'Sessions',
		'Users'
	);

	function __construct($args){
		unset($this->id);
		unset($this->name);
		unset($this->deleted);
		unset($this->deleted_at);
		unset($this->date_created);
		unset($this->created_by);
		unset($this->date_modified);
		unset($this->modified_by);
		parent::__construct($args);
	}

	public static function authenticate($username,$password){
		$model = static::model('Users');
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