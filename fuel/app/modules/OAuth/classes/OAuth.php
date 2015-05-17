<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/25/15
 * Time: 9:09 PM
 */

namespace Oauth;


class Oauth extends \UNBOXAPI\Module {

    protected static $_name = 'Oauth';
    protected static $_models = array(
                        'AccessTokens',
                        'AuthCodes',
                        'Clients',
                        'RedirectUris',
                        'RefreshTokens',
                        'Scopes',
                        'Sessions'
                    );

    protected $authorization_server;
    protected $resource_server;

    private $accessTokenStorage;
    private $authCodeStorage;
    private $clientStorage;
    private $refreshTokenStorage;
    private $scopeStorage;
    private $sessionStorage;

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }
    public function __construct(){
        unset($this->id);
        unset($this->name);
        unset($this->deleted);
        unset($this->deleted_at);
        unset($this->date_created);
        unset($this->created_by);
        unset($this->date_modified);
        unset($this->modified_by);

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
        try{
            if ($token!==null){
                return $this->resource_server->isValidRequest(true,$token);
            }
            return $this->resource_server->isValidRequest();
        }catch(\Exception $ex){
            \Log::error("Invalid Token: $token - Exception ".$ex->getMessage());
            return false;
        }

    }
    public function getTokenUserId(){
        return $this->resource_server->getAccessToken()->getSession()->getOwnerId();
    }
    public function getTokenSessionId(){
        return $this->resource_server->getAccessToken()->getSession();
    }
}