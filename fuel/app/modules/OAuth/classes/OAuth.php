<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/25/15
 * Time: 9:09 PM
 */

namespace Oauth;


class Oauth {

    protected static $_name = 'Oauth';
    protected static $_config;

    protected $authorization_server;
    protected $resource_server;

    private $accessTokenStorage;
    private $authCodeStorage;
    private $clientStorage;
    private $refreshTokenStorage;
    private $scopeStorage;
    private $sessionStorage;

    public static function models(){
        return array(
            'AccessTokens',
            'AuthCodes',
            'Clients',
            'RedirectUris',
            'RefreshTokens',
            'Scopes',
            'Sessions'
        );
    }
    public static function config(){
        static::$_config = \Config::load(static::$_name."::module");
        return static::$_config;
    }
    public static function seeds()
    {
        $config = static::config();
        if (isset($config['seed_models'])) {
            return $config['seed_models'];
        }
        return false;
    }
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    protected function __construct(){
        $this->initStorage();
        $this->initAuthServer();
        $this->initResourceServer();
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
        $this->authorization_server = new \League\OAuth2\Server\AuthorizationServer;
        $this->authorization_server->setSessionStorage($this->sessionStorage);
        $this->authorization_server->setAccessTokenStorage($this->accessTokenStorage);
        $this->authorization_server->setClientStorage($this->clientStorage);
        $this->authorization_server->setScopeStorage($this->scopeStorage);
        $this->authorization_server->setRefreshTokenStorage($this->refreshTokenStorage);
        $this->authorization_server->setAuthCodeStorage($this->authCodeStorage);
    }
    private function initResourceServer(){
        $this->resource_server = new \League\OAuth2\Server\ResourceServer(
            $this->sessionStorage,
            $this->accessTokenStorage,
            $this->clientStorage,
            $this->scopeStorage
        );
    }
    public function getAuthServer()
    {
        return $this->authorization_server;
    }
    public function getResourceServer()
    {
        return $this->resource_server;
    }
    public function setupGrant($grant){
        switch ($grant) {
            case 'password':
                $Grant = new \League\OAuth2\Server\Grant\PasswordGrant();
                $Grant->setVerifyCredentialsCallback(function ($username, $password) {
                    $password = base64_decode($password);
                    $user = \Users\User::authenticate($username,$password);
                    if ($user===false){
                        return $user;
                    }else{
                        return $user->id;
                    }
                });
                $this->authorization_server->addGrantType($Grant);
                break;
            case 'refresh_token':
                $Grant = new \League\OAuth2\Server\Grant\RefreshTokenGrant();
                $this->authorization_server->addGrantType($Grant);
                break;
            case 'client':
                $Grant = new \League\OAuth2\Server\Grant\ClientCredentialsGrant();
                $this->authorization_server->addGrantType($Grant);
                break;
            case 'authCode':
                $Grant = new \League\OAuth2\Server\Grant\AuthCodeGrant();
                $this->authorization_server->addGrantType($Grant);
                break;
        }
    }
    public function issueAccessToken(){
        return $this->authorization_server->issueAccessToken();
    }
    public function validateToken($token=null){
        if ($token!==null){
            return $this->resource_server->isValidRequest(true,$token);
        }
        return $this->resource_server->isValidRequest();
    }
    public function getTokenUserId(){
        return $this->resource_server->getAccessToken()->getSession()->getOwnerId();
    }
}