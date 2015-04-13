<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/28/15
 * Time: 12:30 PM
 */

namespace Controller;


class User extends \Controller_Rest{

    public $oauth_server;

    public function router($resource,$arguments){
        try{
            $this->oauth_server = \Oauth\Oauth::getInstance();
            parent::router($resource, $arguments);
        }catch(\Exception $ex){
            $response = \Response::forge(\Format::forge(array('Error' => $ex->getMessage()))->to_json(), 500)->set_header('Content-Type', 'application.json');
            return $response;
        }
    }

    public function post_login(){
        try {
            //TODO::Move grant, client id and client secret to OAuth\Client object
            $_POST['grant_type'] = 'password';
            $_POST['client_id'] = \Config::get('unbox.client.id');
            $_POST['client_secret'] = \Config::get('unbox.client.secret');

            if (isset($_POST['username'])&&isset($_POST['password'])){
                $this->oauth_server->setupGrant('password');
                $this->oauth_server->setupGrant('refreshToken');
                //TODO:Return encrypted cookie
                $response = $this->oauth_server->issueAccessToken();

            }else{
                throw new \Exception("Username and password must be provided");
            }
            return $this->response(
                $response
            );
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
    public function post_token(){
        //TODO: Actual OAuth token request goes here
    }
    public function post_register($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
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
                            'err' => 'true',
                            'msg' => "ReCaptcha not valid. Errors:".json_encode($errors),
                        ),
                        400
                    );
                }
                return $this->response(
                    $response
                );
            }else{
                switch ($action){

                }
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
    public function post_refresh(){

    }
    public function post_authorization(){

    }
    public function get_me(){
        try {
            if ($this->oauth_server->validToken()){
                $userId = $this->oauth_server->getUserId();
                $response = \Users\User::me($userId);
                return $this->response(
                    $response
                );
            }
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Exception: " . $e->getMessage() . "\n",
                ),
                403
            );
        }
    }
}