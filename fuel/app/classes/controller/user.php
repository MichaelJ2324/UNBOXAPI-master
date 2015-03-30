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
            $_POST['grant_type'] = 'password';
            $_POST['client_id'] = 'unboxapi.com';
            $_POST['client_secret'] = 'secret';

            if (isset($_POST['username'])&&isset($_POST['password'])){
                $this->oauth_server->setupGrant('password');
                $this->oauth_server->setupGrant('refresh_token');
                $response = $this->oauth_server->getAccessToken();
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
}