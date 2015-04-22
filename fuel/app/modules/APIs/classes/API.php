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

    public $version;
    public $web_address;
    public $login_required;

    private $response;
    private $request;

    function __construct($url,$id=""){
        $this->model = new Model\Apis();
        $this->web_address = rtrim($url,"/");;
        if ($id!==""){
            if(!$this->retrieveApi($id)){
                return false;
            }
        }
    }

    public static function methods($id=""){
        $api = new Model\Apis();
        return $api->getHttpMethods($id);
    }
    public static function entryPoints($id,$httpMethod=""){
        $api = new Model\Apis();
        return $api->getEntryPoints($id,$httpMethod);
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
    public static function login($id,$entryPoint,$data=array()){
        if (count($data)==0){
            $data = \Input::post();
        };
        $url=$data['web_address'];
        if ($url!==false){
            $Api = new Api($url,$id);
            $entryPoint = new \EntryPoints\EntryPoint($entryPoint);
            $Api->request = new Request($Api->web_address);
            $Api->request->set_EntryPoint($entryPoint,$data);
            return $Api->request->send();
        }else{
            return false;
        }
    }
    public static function buildScript($id,$entryPoint,$data=array()){
        if (count($data)==0){
            $data = \Input::post();
        };
        $url=$data['web_address'];
        if ($url!==false){
            $Api = new Api($url,$id);
            $entryPoint = new \EntryPoints\EntryPoint($entryPoint);
            //$Api->writeScript();
        }else{
            return false;
        }
    }
    public static function test($id,$entryPoint,$data=array()){
        if (count($data)==0){
            $data = \Input::post();
        };
        $url=$data['web_address'];
        if ($url!==false){
            $Api = new Api($url,$id);
            $entryPoint = new \EntryPoints\EntryPoint($entryPoint);
            $Api->request = new Request($Api->web_address);
            $Api->request->set_EntryPoint($entryPoint,$data);
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