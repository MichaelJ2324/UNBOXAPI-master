<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/8/15
 * Time: 3:01 PM
 */

namespace Users;


class User extends \UNBOXAPI\Module{
    protected static $_name = "Users";
    protected static $_label = "User";
    protected static $_label_plural = "Users";

    public $first_name;
    public $last_name;
    public $password;
    public $username;
    public $email;

    public static function authenticate($username,$password,$crypt = true){
        $model = static::model();
        if ($crypt) $password = \Crypt::encode($password);
        $user = $model::query()->where('username', $username )->where('password',$password)->get_one();
        $count = count($user);
        if ($count===1){
            return $user;
        }else{
            return false;
        }
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
        $model = static::model();
        $user = $model::find($userId);
        return $user->to_array();
    }
}