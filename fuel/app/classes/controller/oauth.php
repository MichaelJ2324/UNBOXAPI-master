<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 4/10/15
 * Time: 12:55 PM
 */

namespace Controller;


class oauth extends \Controller_Rest {

    public $oauth_server;

    public function router($resource, $arguments) {
        try{
            $this->oauth_server = \Oauth\Oauth::getInstance();
            parent::router($resource, $arguments);
        }catch(\Exception $ex){
            $response = \Response::forge(\Format::forge(array('Error' => $ex->getMessage()))->to_json(), 500)->set_header('Content-Type', 'application.json');
            return $response;
        }
    }

    public function get_validate($token){
        try {

            if (isset($token)){
                $response = $this->oauth_server->validateToken($token);
            }else{
                throw new \Exception("Missing all required parameters for authorization.");
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
        try {

            if (isset($_POST['username'])&&
                isset($_POST['password'])&&
                isset($_POST['grant_type'])&&
                isset($_POST['client_id'])&&
                isset($_POST['client_secret'])
            ){
                $this->oauth_server->setupGrant('password');
                $this->oauth_server->setupGrant('refreshToken');
                $response = $this->oauth_server->issueAccessToken();
            }else{
                throw new \Exception("Missing all required parameters for authorization.");
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
    public function post_refresh(){
        try {
            if (isset($_POST['refresh_token'])&&
                isset($_POST['grant_type'])&&
                isset($_POST['client_id'])&&
                isset($_POST['client_secret'])
            ){
                $this->oauth_server = \Oauth\Oauth::getInstance();
                $this->oauth_server->setupGrant('refreshToken');
                $response = $this->oauth_server->issueAccessToken();
            }else{
                throw new \Exception("Missing all required parameters for authorization.");
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
    public function post_authorization(){

    }
}