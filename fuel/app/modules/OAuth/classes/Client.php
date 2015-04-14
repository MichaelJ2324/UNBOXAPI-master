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
    public static function generateCookie($tokenInfo){
        $serializedToken = serialize($tokenInfo);
        $data = \Crypt::encode($serializedToken);
        \Cookie::set('unauth',$data);
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
    public function setGrantType($grant){
        if (in_array($grant,$this->config['grant_types'])){
            $this->_grant_type = $grant;
        }else{
            return false;
        }
    }
    public function validateToken(){
        $unauth = \Cookie::get('unauth');
        if ($unauth!==null){
            $tokenInfo = unserialize(\Crypt::decode($unauth));
            if ($this->_server=='localhost'){
                if ($this->server->validateToken($tokenInfo['access_token'])){
                    $this->_token = $tokenInfo;
                }else{
                    return false;
                }
            }else{
                $this->setupRequest("validate/".$tokenInfo['access_token'],"GET");
                if ($this->sendRequest()!==false){
                    $response = $this->request->getResponse();
                    $this->_token = $tokenInfo;
                    $this->_userId = $response['user'];
                }else{
                    return false;
                }
            }
            return true;
        }else{
            return false;
        }
    }
    public function getTokenUser(){
        if ($this->_server=='localhost'){
            $this->_userId = $this->server->getTokenUserId();
        }
        return $this->_userId;
    }
    public function getToken(){
        return $this->_token;
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
            return $this->sendRequest();
        }
    }
    private function setupPayload(){
        $clientAttributes = array(
            'client_id' => $this->_id,
            'client_secret' => $this->_secret,
            'grant_type' => $this->_grant_type
        );
        $this->payload = array_merge($this->payload,$clientAttributes);
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