<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 4/7/15
 * Time: 1:11 AM
 */

namespace Oauth;


class Client {

    protected static $_name = 'Oauth';
    protected static $_config;

    protected $_server;
    protected $_id;
    protected $_secret;
    protected $_grant_type;

    protected $_token;
    protected $_userId;

    public $payload;

    private $config;
    private $request;
    private $server = null;

    public static function config(){
        static::$_config = \Config::load(static::$_name."::module");
        return static::$_config;
    }
    public static function encryptCookie($tokenInfo){
        $serializedToken = serialize($tokenInfo);
        $data = \Crypt::encode($serializedToken);
        \Cookie::set('unauth',$data);
    }
    public static function decryptCookie(){
        $unauth = \Cookie::get('unauth');
        if ($unauth!==null) {
            $serializeToken = \Crypt::decode($unauth);
            $token = unserialize($serializeToken);
            return $token;
        }
        return false;
    }

    public function __construct(){
        $this->setId();
        $this->setSecret();
        $this->setServer();
        $this->config = static::config();
        if ($this->_server=='localhost'){
            $this->server = Oauth::getInstance();
        }
    }
    public function setupSession($loggedIn){
        session_start();
        $_SESSION['loggedIn'] = $loggedIn;
        if ($loggedIn) {
            $_SESSION['token'] = $this->_token;
            $_SESSION['user_id'] = $this->_userId;
        }else{
            unset($_SESSION['token']);
            unset($_SESSION['user_id']);
        }
        session_write_close();
    }
    public function setGrantType($grant){
        if (in_array($grant,$this->config['grant_types'])){
            $this->_grant_type = $grant;
        }else{
            return false;
        }
    }
    public function validateAuth(){
        $token = static::decryptCookie();
        $valid = false;
        if (!($token==false||$token==null||empty($token))){
            if ($this->validateToken($token['access_token'])){
                $this->_token = $token;
                if ($this->getTokenUser()!==false){
                    $valid = true;
                }
            }else{
                if ($valid===false&&isset($token['refresh_token'])){
                    $newToken = $this->refreshToken($token['refresh_token']);
                    if ($newToken!==null){
                        if ($this->validateToken($newToken['access_token'])){
                            $this->_token = $newToken;
                            static::encryptCookie($this->_token);
                            if ($this->_server=='localhost') {
                                if ($this->getTokenUser($newToken['access_token'])!==false) {
                                    $valid = true;
                                }
                            }else{
                                $valid = true;
                            }
                        }
                    }
                }
            }
        }
        $this->setupSession($valid);
        return $valid;
    }
    public function getToken(){
        return $this->_token;
    }
    public function getUserId(){
        return $this->_userId;
    }
    private function validateToken($accessToken){
        $valid = false;
        if ($this->_server=='localhost'){
            if ($this->server->validateToken($accessToken)){
                $valid = true;
            }
        }else{
            $user = $this->getTokenUser($accessToken);
            if ($user!==false){
                $valid = true;
            }
        }
        return $valid;
    }
    private function getTokenUser($accessToken=null){
        $userId = false;
        if ($this->_server=='localhost') {
            $userId = $this->server->getTokenUserId();
            if (!($userId == false || $userId == null)) {
                $this->_userId = $userId;
            }
        }else{
            $this->payload = array(
                'access_token' => $accessToken
            );
            $this->setupPayload();
            $this->setupRequest("me/","POST");
            $response = $this->sendRequest();
            if ($response['err']!==false){
                $userId = $response['id'];
                $this->_userId = $userId;
            }
        }
        return $userId;
    }
    public function issueAccessToken(){
        $this->setupPayload();
        if ($this->_server=='localhost'){
            foreach($this->payload as $param => $value){
                $_POST[$param] = $value;
            }
            switch ($this->_grant_type){
                case "password":
                    $this->server->setupGrant("password");
                case "refresh_token":
                    $this->server->setupGrant("refresh_token");
                    break;
                default:
                    return false;
            }
            return $this->server->issueAccessToken();
        }else{
            $this->setupRequest();
            $response = $this->sendRequest();
            if (!isset($response['err'])){
                return $response;
            }else{
                throw new \Exception("Access Token not issued. Server Response - ".$response['msg']);
            }
        }
    }
    public function logout(){
        $this->destroySession();

    }
    private function destroySession(){
        $_SESSION = array();
        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // Finally, destroy the session.
        session_destroy();
    }
    private function refreshToken($refresh_token=null){
        $this->setGrantType("refresh_token");
        if ($refresh_token==null){
            $this->payload = array(
                'refresh_token' => $this->_token['refresh_token']
            );
        }else{
            $this->payload = array(
                'refresh_token' => $refresh_token
            );
        }
        $token = $this->issueAccessToken();
        return $token;
    }
    private function setupPayload(){
        $clientAttributes = array(
            'client_id' => $this->_id,
            'client_secret' => $this->_secret,
            'grant_type' => $this->_grant_type
        );
        if (is_array($this->payload)){
            $this->payload = array_merge($this->payload,$clientAttributes);
        }else{
            $this->payload = $clientAttributes;
        }

    }
    private function sendRequest(){
        if (isset($this->payload)){
            if (is_array($this->payload)){
                $this->request->payload = $this->payload;
                $this->request->initialize();
                if ($this->request->send()){
                    return $this->request->getResponse();
                }else{
                    throw new \Exception("Error");
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    private function setId(){
        $this->_id = \Config::get('unbox.oauth.client.id');
        return $this->_id;
    }
    private function setSecret(){
        $this->_secret = \Config::get('unbox.oauth.client.secret');
        return $this->_secret;
    }
    private function setServer(){
        $this->_server = \Config::get('unbox.oauth.server.host');
        return $this->_server;
    }
    private function buildURL(){
        $url = "";
        switch ($this->_grant_type){
            case "password":
                $url = "token";
                break;
            case "refresh_token":
                $url = "refresh";
                break;
        }
        return $this->_server.DIRECTORY_SEPARATOR."oauth".DIRECTORY_SEPARATOR.$url;
    }
    private function setupRequest($url=null,$httpMethod="POST"){
        if ($url!==null) {
            $url = $this->buildURL();
        }else{
            $url = $this->_server.DIRECTORY_SEPARATOR."oauth".DIRECTORY_SEPARATOR.$url;
        }
        $this->request = new \Request\Rest($url,$httpMethod,true,false);
        return $this->request;
    }
}