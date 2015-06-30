<?php

namespace OAuth;

class Server {

	protected static $_name = 'OAuth';
	protected static $_config;

    protected $authorization_server;
    protected $resource_server;

	private $config;
    private $accessTokenStorage;
    private $authCodeStorage;
    private $clientStorage;
    private $refreshTokenStorage;
    private $scopeStorage;
    private $sessionStorage;
	private $requestHandler;

	public static function config(){
		static::$_config = \Config::load(APPPATH."modules/".static::$_name."/config/module.php");
		return static::$_config;
	}

    public static function getInstance($autoInit = false)
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static($autoInit);
        }
        return $instance;
    }
    protected function __construct($autoInit = false){
		$this->requestHandler = new RequestHandler();
        $this->initStorage();
		$this->config = static::config();
		if ($autoInit){
			$this->initAuthServer();
			$this->initResourceServer();
		}
    }
    private function __clone()
    {
    }
    private function __wakeup()
    {
    }
    private function initStorage(){
        $this->accessTokenStorage = new \Oauth\Storage\AccessToken();
        $this->authCodeStorage = new \Oauth\Storage\AuthCode();
        $this->clientStorage = new \Oauth\Storage\Client();
        $this->refreshTokenStorage = new \Oauth\Storage\RefreshToken();
        $this->scopeStorage = new \Oauth\Storage\Scope();
        $this->sessionStorage = new \Oauth\Storage\Session();
    }
    private function initAuthServer(){
        $this->authorization_server = new \OAuth2\Server\AuthorizationServer($this->requestHandler);
        $this->authorization_server->setSessionStorage($this->sessionStorage);
        $this->authorization_server->setAccessTokenStorage($this->accessTokenStorage);
        $this->authorization_server->setClientStorage($this->clientStorage);
        $this->authorization_server->setScopeStorage($this->scopeStorage);
        $this->authorization_server->setRefreshTokenStorage($this->refreshTokenStorage);
        $this->authorization_server->setAuthCodeStorage($this->authCodeStorage);
		$this->authorization_server->requireScopeParam(true);
    }
    private function initResourceServer(){
        $this->resource_server = new \OAuth2\Server\ResourceServer(
			$this->requestHandler,
            $this->sessionStorage,
            $this->accessTokenStorage,
            $this->clientStorage,
            $this->scopeStorage
        );
    }
    public function setupGrant($grant){
		if (!is_object($this->authorization_server)){
			$this->initAuthServer();
		}
        switch ($grant) {
            case 'password':
                $Grant = new \OAuth2\Server\Grant\PasswordGrant();
                $Grant->setVerifyCredentialsCallback(function ($username, $password) {
                    $password = base64_decode($password);
                    $user = \OAuth\User::authenticate($username,$password);
                    if ($user===false){
                        return $user;
                    }else{
                        return $user->id;
                    }
                });
                $this->authorization_server->addGrantType($Grant,$grant);
				$this->setupGrant('refresh_token');
				break;
            case 'refresh_token':
                $Grant = new \OAuth2\Server\Grant\RefreshTokenGrant();
                $this->authorization_server->addGrantType($Grant,$grant);
                break;
			case 'client':
				$Grant = new \OAuth2\Server\Grant\ClientCredentialsGrant();
				$this->authorization_server->addGrantType($Grant,$grant);
				$this->setupGrant('refresh_token');
				break;
            case 'auth_code':
                $Grant = new \OAuth2\Server\Grant\AuthCodeGrant();
                $this->authorization_server->addGrantType($Grant,$grant);
                break;
			default:
				$this->setupGrant('client');
        }
    }
    public function issueAccessToken(){
		if (!is_object($this->authorization_server)){
			$this->initAuthServer();
		}
		$this->setDefaultScopes();
		$this->sanitizeScopes();
        return $this->authorization_server->issueAccessToken();
    }
	private function setDefaultScopes(){
		$clientType = $this->getClientType();
		if ($this->authorization_server->hasGrantType('password')){
			switch ($clientType){
				case 'api_user':
				case 'unbox_client':
					$this->authorization_server->setDefaultScope('api');
					break;
			}
		}else if ($this->authorization_server->hasGrantType('client')){
			switch ($clientType){
				case 'api_user':
					$this->authorization_server->setDefaultScope('api');
					break;
				case 'unbox_client':
					$this->authorization_server->setDefaultScope('client');
					break;
			}
		}
	}
	private function sanitizeScopes(){
		$scope = \Input::post('scope');
		$scopes = explode(",",$scope);
		$sanitizedScopes = array();
		foreach($scopes as $sc){
			if (in_array($sc,$this->config['scopes'])){
				if ($sc == 'admin' || $sc == 'client'){
					if ($this->getClientType()=='unbox_client'){
						$sanitizedScopes[] = $sc;
					}
				}else{
					$sanitizedScopes[] = $sc;
				}
			}
		}
		$_POST['scope'] = implode(",",$sanitizedScopes);
	}
    public function validateToken($token=null){
        try{
			if (!is_object($this->resource_server)){
				$this->initResourceServer();
			}
            if ($token!==null){
                return $this->resource_server->isValidRequest(true,$token);
            }
            return $this->resource_server->isValidRequest();
        }catch(\Exception $ex){
            \Log::info("Invalid Token: $token - Exception ".$ex->getMessage());
            return false;
        }
    }
    public function getTokenUser(){
		if (!is_object($this->resource_server)){
			$this->initResourceServer();
		}
		$type = $this->getTokenOwnerType();
		if ($type === 'user') {
			return $this->getTokenOwnerId();
		}else{
			$client = $this->getTokenOwnerId();
			$User = Model\Users::query()->where('api_client_id',$client)->get_one();
			return $User->id;
		}
    }
	private function getTokenOwnerId(){
		if (!is_object($this->resource_server)){
			$this->initResourceServer();
		}
		return $this->resource_server->getAccessToken()->getSession()->getOwnerId();
	}
	private function getTokenOwnerType(){
		if (!is_object($this->resource_server)){
			$this->initResourceServer();
		}
		return $this->resource_server->getAccessToken()->getSession()->getOwnerType();
	}
	public function getTokenScopes(){
		if (!is_object($this->resource_server)){
			$this->initResourceServer();
		}
		$ScopeEntities = $this->resource_server->getAccessToken()->getScopes();
		$scopes = array();
		foreach($ScopeEntities as $scope){
			$scopes[] = $scope->getId();
		}
		return $scopes;
	}
	public function revokeToken($token){
		if (!is_object($this->resource_server)){
			$this->initResourceServer();
		}
		$this->resource_server->setRefreshTokenStorage($this->refreshTokenStorage);
		$refreshToken = $this->resource_server->getRefreshTokenStorage()->get($token);
		$accessToken = $refreshToken->getAccessToken();
		$session = $accessToken->getSession();
		$accessToken->expire();
		$refreshToken->expire();
		$this->resource_server->getSessionStorage()->delete($session->getId());
	}
	private function getClientType(){
		$clientID = $this->requestHandler->getParam("client_id");
		$clientSecret = $this->requestHandler->getParam('client_secret');
		if (!is_null($clientID) && !is_null($clientSecret)){
			$Client = Model\Clients::query()->where('client_id',$clientID)->where('secret',$clientSecret)->get_one();
			if ($Client!==null){
				return $Client->type;
			}
		}else{
			throw new \Exception("Missing parameters in request. Check Client ID and Client Secret");
		}
	}
}