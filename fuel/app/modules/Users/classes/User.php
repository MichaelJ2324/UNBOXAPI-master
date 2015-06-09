<?php

namespace Users;


class User extends \UNBOXAPI\Module{
    protected static $_name = "Users";
    protected static $_label = "User";
    protected static $_label_plural = "Users";

	protected static $_models = array(
		'Users',
		'Preferences'
	);

	protected static $_available_attributes = array(
		'first_name',
		'last_name',
	);
	public $preferences;

	public function __construct(){
		unset($this->name);
		unset($this->deleted_at);
		unset($this->date_created);
		unset($this->created_by);
		unset($this->date_modified);
		unset($this->modified_by);
	}

    public static function register(){
        $User = new User();
        $User->first_name = \Input::json('first_name');
        $User->last_name = \Input::json('last_name');
        $User->name = $User->first_name." ".$User->last_name;
        $User->username = \Input::json('username');
        $User->email = \Input::json('email');
        $password = \Input::json('password');
        $password = base64_decode($password);
        $User->password = \Crypt::encode($password);

        return static::create($User);
    }
    public static function me($userId){
        $model = static::model(true);
        $user = $model::find($userId);
        return $user->to_array();
    }
}