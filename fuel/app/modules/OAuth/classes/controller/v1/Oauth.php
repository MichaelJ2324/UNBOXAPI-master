<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 5/19/15
 * Time: 4:48 PM
 */

namespace OAuth\Controller\V1;

class OAuth extends \Controller_Rest{

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

    public function post_me(){
        try {
            if (isset($_POST['client_id'])&&
                isset($_POST['client_secret'])&&
                isset($_POST['access_token'])
            ){
                if ($this->oauth_server->validateToken($_POST['access_token'])){
                    $userId = $this->oauth_server->getTokenUserId();
                    $response = \Users\User::me($userId);
                }else{
                    $response = array(
                        'err' => true,
                        'msg' => 'Invalid Access Token.'
                    );
                }
            }else{
                throw new \Exception("Missing all required parameters for authorization.");
            }
            return $this->response(
                $response
            );
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => true,
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
                $this->oauth_server->setupGrant('refresh_token');
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
                    'err' => true,
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
                $this->oauth_server->setupGrant('refresh_token');
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
                    'err' => true,
                    'msg' => "Error: " . $e->getMessage() . "\n",
                ),
                401
            );
        }
    }
    public function post_authorization(){

    }
    public function post_revoke(){

    }
}