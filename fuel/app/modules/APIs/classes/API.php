<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 6/17/14
 * Time: 11:45 PM
 */

namespace Apis;

class Api extends \UNBOXApi\Module{

    protected static $_name = "Apis";
    protected static $_label = "API";
    protected static $_label_plural = "APIs";
    protected static $_models = array(
        'Apis',
        'Entrypoints',
        'Logins'
    );

    public $version;
    public $web_address;
    public $login_required;

    private $response;
    private $request;

    public static function methods($id=""){
        $api = new Model\Apis();
        return $api->getHttpMethods($id);
    }
    public static function entrypoints($id,$httpMethod=""){
        $api = new Model\Apis();
        return $api->getEntrypoints($id,$httpMethod);
    }
    public static function logins($id){
        $api = new Model\Apis();
        return $api->getLogins($id);
    }
    private function retrieveApi($id){
        $result = $this->model->getApi($id);
        if (count($result)>0){
            foreach($result as $row){
                $this->id = $id;
                $this->name = $row['name'];
                $this->version = $row['version'];
                $this->web_address .= "/" . ltrim($row['url'],"/");
                $this->login_required = $row['login_required'];
            }
            return true;
        }else{
            return false;
        }
    }
    public static function login($id,$entrypoint,$data=array()){
        if (count($data)==0){
            $data = \Input::post();
        };
        $url=$data['web_address'];
        if ($url!==false){
            $Api = new Api($url,$id);
            $entrypoint = new \Entrypoints\Entrypoint($entrypoint);
            $Api->request = new Request($Api->web_address);
            $Api->request->set_Entrypoint($entrypoint,$data);
            return $Api->request->send();
        }else{
            return false;
        }
    }
    public static function buildScript($id,$entrypoint,$data=array()){
        if (count($data)==0){
            $data = \Input::post();
        };
        $url=$data['web_address'];
        if ($url!==false){
            $Api = new Api($url,$id);
            $entrypoint = new \Entrypoints\Entrypoint($entrypoint);
            //$Api->writeScript();
        }else{
            return false;
        }
    }
    public static function test($id,$entrypoint,$data=array()){
        if (count($data)==0){
            $data = \Input::post();
        };
        $url=$data['web_address'];
        if ($url!==false){
            $Api = new Api($url,$id);
            $entrypoint = new \Entrypoints\Entrypoint($entrypoint);
            $Api->request = new Request($Api->web_address);
            $Api->request->set_Entrypoint($entrypoint,$data);
            if ($Api->login_required==true) {
                $Api->request->token = $data['token'];
            }
            return $Api->request->send();
        }else{
            return false;
        }
    }
    public function writeScript(){

    }
} 