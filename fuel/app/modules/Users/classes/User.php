<?php

namespace Users;

use OAuth\Client as OAuthClient;
use \UNBOXAPI\Box\Module;
use \UNBOXAPI\Data\Util\Guid;

class User extends Module {

	protected static $_canisters = array(
		'Users',
		'Preferences',
		'VerificationCodes'
	);

	public function __construct(){
		unset($this->name);
		unset($this->deleted_at);
		unset($this->date_created);
		unset($this->created_by);
		unset($this->date_modified);
		unset($this->modified_by);
		parent::__construct();
	}

	public static function registered($username,$email){
		$model = static::model(true);
		$User = $model::query()->where("username",strtolower($username))->get_one();
		if (count($User) > 0){
			throw new \Exception("Username is already registered. \n", 400);
		}else{
			$User = $model::query()->where("primary_email",strtolower($email))->or_where("secondary_email",strtolower($email))->get_one();
			if (count($User) > 0){
				throw new \Exception("Email is already registered. \n", 400);
			}
		}
		return false;
	}

    public static function register(){
		$username = \Input::json('username');
		$email = \Input::json('email');
		if (!static::registered($username,$email)) {
			$User = new static();
			$User->setProperty("username", strtolower($username));
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$User->setProperty("primary_email", strtolower($email));
			}else{
				throw new \Exception("Invalid email address. Please enter a valid email address.",400);
			}
			$User->setProperty("verified",0);
			$User->id = Guid::make();
			$expire = time() + 86400;
			$VerificationCode = Model\VerificationCodes::forge(
				array(
					'code' => Guid::make(),
					'user_id' => $User->id,
					'expire_time' => $expire
				)
			);

			$Email = \Email::forge();
			$Email->to($email);
			$Email->subject("Verify UNBOX API Account");
			$Email->priority(\Email::P_HIGH);
			$Body = \View::forge("email/verification",array(
				'code' => $VerificationCode->code
			))->render();
			$Email->html_body($Body);
			try{
				$Email->send();
				$User->save();
				$VerificationCode->save();
			}
			catch(\EmailValidationFailedException $e)
			{
				throw new \Exception("Invalid email address. Please enter a valid email address.",400);
			}
			catch(\EmailSendingFailedException $e)
			{
				\Log::fatal("Verificaiton Email failed: ".$e->getMessage());
				throw new \Exception("An internal error occurred. Please contact system administrator.",500);
			}
		}
    }
    public static function me(){
		$userId = OAuthClient::user('id');
		if (!empty($userId)) {
			$model = static::model(TRUE);
			$user  = $model::find($userId);
			return $user->to_array();
		}else{
			throw new \Exception("User is not found.");
		}
    }
}