<?php

namespace OAuth\Controller\V1;

class OAuth extends \Controller_Rest{

    protected $server;

    public function router($resource, $arguments) {
        try{
			$oauth_server = \Config::get('unbox.oauth.server.host');
			if ($oauth_server =='localhost'){
				$this->server = \OAuth\Server::getInstance();
				return parent::router($resource, $arguments);
			}else{
				$url = $oauth_server.DIRECTORY_SEPARATOR."oauth/v1/oauth/".$resource;
				$Request = \Request::forge($url,'curl');
				$method = \Input::method();
				$Request->set_method($method);
				$Request->set_params($_POST);
				foreach(\Input::headers() as $header => $value){
					$Request->set_header($header,$value);
				}
				return $Request->execute($arguments)->response();
			}
        }catch(\Exception $ex){
            $response = \Response::forge(\Format::forge(array('Error' => $ex->getMessage()))->to_json(), 500)->set_header('Content-Type', 'application.json');
            return $response;
        }
    }
    public function action_token($client_id="",$client_secret="",$grant_type="",$scope=""){
        try {
			$this->server->setupGrant($grant_type);
			$response           = $this->server->issueAccessToken();
			return $this->response(
				$response
			);
        } catch (\Exception $e) {
            return $this->response(
                "Error: " . $e->getMessage() . "\n",
                401
            );
        }
    }
    public function action_refresh($client_id="",$client_secret="",$grant_type="",$scope="",$refresh_token=""){
        try {
			$this->server->setupGrant($grant_type);
			$response = $this->server->issueAccessToken();
            return $this->response(
                $response
            );
        } catch (\Exception $e) {
            return $this->response(
                "Error: " . $e->getMessage() . "\n",
                401
            );
        }
    }
    public function action_auth(){
		try {
			$this->server->setupGrant('auth_code');
			$authParams = $this->server->getGrantType('authorization_code')->checkAuthorizeParams();

			return \Response::redirect('login');
		} catch (\Exception $e) {
			return $this->response(
				"Error: " . $e->getMessage() . "\n",
				401
			);
		}
    }
    public function action_revoke($token=""){
		try {
			if (!isset($_POST['token'])){
				$_POST['token'] = $token;
			}
			$this->server->revokeToken($token);
			return true;
		} catch (\Exception $e) {
			return $this->response(
				"Error: " . $e->getMessage() . "\n",
				401
			);
		}
    }
	public function action_user($token=null){
		try{
			if (!isset($_POST['token'])){
				$_POST['token'] = $token;
			}
			if ($this->server->validateToken($token)){
				return $this->response(
					array(
						'id' => $this->server->getTokenUser(),
						'scopes' => $this->server->getTokenScopes()
					)
				);
			}
		}catch (\Exception $ex){
			return $this->response(
				"Error: " . $ex->getMessage() . "\n",
				401
			);
		}
	}
}