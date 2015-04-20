<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/28/15
 * Time: 12:30 PM
 */

namespace Controller;


use Oauth\Oauth;

class User extends \Controller_Rest{

    public $oauth_client;

    public function router($resource, $arguments) {
        try{
            $this->oauth_client = new \Oauth\Client();
            parent::router($resource, $arguments);
        }catch(\Exception $ex){
            $response = \Response::forge(\Format::forge(array('Error' => $ex->getMessage()))->to_json(), 500)->set_header('Content-Type', 'application.json');
            return $response;
        }
    }

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
            if ($this->oauth_client->validateToken()){
                $userId = $this->oauth_client->getUserId();
                $response = \Users\User::me($userId);
                $response['token'] = $this->oauth_client->getToken();
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
}