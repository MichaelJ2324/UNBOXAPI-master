<?php

namespace Users\Controller\V1;


use Controller\V1\Rest as RestV1;

class Users extends RestV1{

    protected $auth_required = false;

    public function post_login(){
        try {
			if ($this->authorized==false) {
				if (isset($_POST['username']) && isset($_POST['password'])) {
					if ($this->oauth_client->setGrantType('password') !== FALSE) {
						$tokenInfo                   = $this->oauth_client->issueAccessToken();
						\OAuth\Client::makeCookie($tokenInfo);
						$response = array(
							'err' => FALSE,
							'msg' => "Successfully logged in."
						);
					} else {
						throw new \Exception("Grant type not found");
					}
					return $this->response(
						$response
					);
				} else {
					throw new \Exception("Username and password must be provided");
				}
			}else{
				throw new \Exception("Already logged in as user.");
			}
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Error: " . $e->getMessage() . "\n",
                ),
                401
            );
        }
    }

    public function post_register(){
        try
        {
			if ($this->authorized===FALSE) {
				if (isset($_POST['username']) &&
					isset($_POST['password']) &&
					isset($_POST['last_name']) &&
					isset($_POST['captcha']) &&
					isset($_POST['email'])
				) {
					$captcha   = \Input::json('captcha');
					$remoteIp  = \Input::ip();
					$recaptcha = new \ReCaptcha\ReCaptcha(\Config::get("unbox.google.recaptcha"));
					$resp      = $recaptcha->verify($captcha, $remoteIp);
					if ($resp->isSuccess()) {
						$response = \Users\User::register();
					} else {
						$errors = $resp->getErrorCodes();
						return $this->response(
							array(
								'err' => TRUE,
								'msg' => "ReCaptcha not valid. Errors:".json_encode($errors),
							),
							400
						);
					}
					return $this->response(
						$response
					);
				} else {
					throw new \Exception("Missing required field for registration.");
				}
			}else{
				throw new \Exception("Already logged in as user.");
			}
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => true,
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function get_me($user_id=null){
        try {
            if ($this->authorized===TRUE){
                $response = \Users\User::me();
            }else {
                throw new \Exception("Access denied.");
            }
            return $this->response(
                $response
            );
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => true,
                    'msg' => "Exception: " . $e->getMessage() . "\n",
                ),
                403
            );
        }
    }
    public function post_logout(){
        try {
            if ($this->authorized===TRUE){
                $response = $this->oauth_client->revokeToken();
            }else {
                throw new \Exception("Access denied.");
            }
            return $this->response(
                $response
            );
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => true,
                    'msg' => "Exception: " . $e->getMessage() . "\n",
                ),
                403
            );
        }
    }


    public function get_index($id="",$action="",$related_module="",$related_id=""){
        return $this->response(
            array(
                'err' => 'true',
                'msg' => "Please use Users/me Entrypoint. \n",
            ),
            401
        );
    }
    public function post_index($id="",$action="",$related_module="",$related_id=""){
        return $this->response(
            array(
                'err' => 'true',
                'msg' => "Please use Users/Register Entrypoint. \n",
            ),
            401
        );
    }
    public function put_index($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($this->authorized){
                if ($action==""||!isset($action)){
					$response = \Users\User::update();
                }else{
                    switch ($action) {
                        default:
                            throw new \Exception("Unknown action provided for parameter 3 of request");
                    }
                }
            }else{
                throw new \Exception("Access denied");
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function delete_index($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($this->authorized) {
                if ($action == "" || !isset($action)) {
                    $response = \Users\User::delete();
                } else {
                    switch ($action) {
                        default:
                            throw new \Exception("Unknown action provided for parameter 3 of request");
                    }
                }
            }else{
                throw new \Exception("Access denied");
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }

}