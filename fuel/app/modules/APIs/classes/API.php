<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 6/17/14
 * Time: 11:45 PM
 */

namespace APIs;

class API extends \UNBOXAPI\Module{

    protected static $_name = "Apis";
    protected static $_label = "API";
    protected static $_label_plural = "Apis";

    public $version;
    public $web_address;
    public $login_required;

    private $response;
    private $request;

    function __construct($url,$id=""){
        $this->model = new Model\APIs();
        $this->web_address = rtrim($url,"/");;
        if ($id!==""){
            if(!$this->retrieveAPI($id)){
                return false;
            }
        }
    }

    public static function methods($id=""){
        $api = new Model\APIs();
        return $api->getHttpMethods($id);
    }
    public static function entryPoints($id,$httpMethod=""){
        $api = new Model\APIs();
        return $api->getEntryPoints($id,$httpMethod);
    }
    public static function logins($id){
        $api = new Model\APIs();
        return $api->getLogins($id);
    }
    private function retrieveAPI($id){
        $result = $this->model->getAPI($id);
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
            $API = new API($url,$id);
            $entryPoint = new \EntryPoints\EntryPoint($entryPoint);
            $API->request = new Request($API->web_address);
            $API->request->set_EntryPoint($entryPoint,$data);
            return $API->request->send();
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
            $API = new API($url,$id);
            $entryPoint = new \EntryPoints\EntryPoint($entryPoint);
            //$API->writeScript();
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
            $API = new API($url,$id);
            $entryPoint = new \EntryPoints\EntryPoint($entryPoint);
            $API->request = new Request($API->web_address);
            $API->request->set_EntryPoint($entryPoint,$data);
            if ($API->login_required==true) {
                $API->request->token = $data['token'];
            }
            return $API->request->send();
        }else{
            return false;
        }
    }
    public function writeScript(){

    }
} 