<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 4/7/15
 * Time: 1:11 AM
 */

namespace Oauth;


class Client {

    protected $_server;
    protected $_id;
    protected $_secret;
    protected $_grant_type;

    public function __construct(){
        $this->setId();
        $this->setSecret();
    }
    public function setGrantType(){

    }
    private function setId(){
        $this->_id = \Config::get('unbox.oauth.client.id');
    }
    private function setSecret(){
        $this->_secret = \Config::get('unbox.oauth.client.secret');
    }
    private function request(){

    }
}