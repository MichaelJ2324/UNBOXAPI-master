<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/19/15
 * Time: 4:48 PM
 */

namespace Users\Controller\V1;


use Controller\V1\Rest as RestV1;

class Users extends RestV1{

    protected $auth_required = false;

    public function post_login(){
        try {
            if (isset($_POST['username'])&&isset($_POST['password'])){
                $payload = array(
                    'username' => $_POST['username'],
                    'password' => $_POST['password']
                );
                if ($this->oauth_client->setGrantType('password')!==false){
                    $this->oauth_client->payload = $payload;
                    $tokenInfo = $this->oauth_client->issueAccessToken();
                    \Oauth\Client::encryptCookie($tokenInfo);
                    $response = array(
                        'err' => false,
                        'msg' => "Successfully logged in."
                    );
                }else{
                    throw new \Exception("Grant type not found");
                }
                return $this->response(
                    $response
                );
            }else{
                throw new \Exception("Username and password must be provided");
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
            if (isset($_POST['username'])&&
                isset($_POST['password'])&&
                isset($_POST['last_name'])&&
                isset($_POST['captcha'])&&
                isset($_POST['email'])
            ){
                $captcha = \Input::json('captcha');
                $remoteIp = \Input::ip();
                $recaptcha = new \ReCaptcha\ReCaptcha(\Config::get("unbox.google.recaptcha"));
                $resp = $recaptcha->verify($captcha, $remoteIp);
                if ($resp->isSuccess()) {
                    $response = \Users\User::register();
                } else {
                    $errors = $resp->getErrorCodes();
                    return $this->response(
                        array(
                            'err' => true,
                            'msg' => "ReCaptcha not valid. Errors:".json_encode($errors),
                        ),
                        400
                    );
                }
                return $this->response(
                    $response
                );
            }else{
                throw new \Exception("Missing required field for registration.");
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
    public function get_me(){
        try {
            if ($_SESSION['loggedIn']){
                $userId = $this->oauth_client->getUserId();
                \Log::debug("Current user: $userId");
                if (isset($userId)) {
                    $response = \Users\User::me($userId);
                }else{
                    throw new \Exception("No user associated with current token.");
                }
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
            if ($_SESSION['loggedIn']){
                $response = $this->oauth_client->logout();
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
            if ($_SESSION['loggedIn']){
                if ($action==""||!isset($action)){
                    $response = \Users\User::update($_SESSION['user_id']);
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
            if ($_SESSION['loggedIn']) {
                if ($action == "" || !isset($action)) {
                    $response = \Users\User::delete($_SESSION['user_id']);
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