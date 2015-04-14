<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 6/14/14
 * Time: 12:09 AM
 */

namespace EntryPoints;


class EntryPoint extends \UNBOXAPI\Module{
    protected static $_name = "EntryPoints";
    protected static $_label = "Entry Point";
    protected static $_label_plural = "Entry Points";

    public $description;
    public $url;
    public $method;
    public $method_name;
    public $api_versions = array();
    public $params = array();

    private $urlParams = array();
    private $requestParams = array();


    function __construct($id=null){
        $this->model = new Model\EntryPoints();
        if (isset($id)&&$id!==""){
            if($this->retrieve_EntryPoint($id)===false){
                return false;
            }else{
                if ($this->retrieve_Params($id)===false){
                    return false;
                }else{
                    if ($this->retrive_ApiVersions($id)===false){
                        return false;
                    }
                }
            }
        }
    }

    public static function get($id=""){
        if ($id==""){
            $id='all';
            $ep = Model\EntryPoints::find($id,array("related"=>array("httpMethod")));
            $ep = static::formatResult($ep);
        }else{
            $ep = Model\EntryPoints::find($id,array("related"=>array("httpMethod")));
        }
        return $ep;
    }

    public static function related($id,$relationship){
        $relatedRecords = array();
        switch ($relationship){
            case "parameters":
                $records = Model\EntryPoints::getParameters($id);
                foreach($records as $row){
                    $row['data_type'] = new \ParameterTypes\ParameterType($row['data_type_name']);
                    $row['api_type'] = new \ParameterTypes\ParameterType($row['api_type_name']);
                    $relatedRecords[]=$row;
                }
                break;
            default:
                throw new \Exception("Invalid Relationship");
        }
        return $relatedRecords;

    }


    public static function getParams($id){
        $ep = new EntryPoint($id);
        return $ep->params;
    }

    private function retrieve_EntryPoint($id){
        $ep = $this->model->getEntryPoint($id);
        if (count($ep)==1){
            foreach($ep as $row){
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->method = $row['method'];
                $this->method_name = $row['method_name'];
                $this->url = $row['url'];
                $this->description = $row['description'];
            }
            return true;
        }else{
            return false;
        }
    }
    private function retrieve_Params($id){
        $ip = $this->model->getParameters($id);
        if ($ip){
            foreach($ip as $row){
                $this->params[] = new \Parameters\Parameter($id,$row['id']);
            }
            return true;
        }else{
            return false;
        }
    }
    private function retrive_ApiVersions($id){
        $rows = $this->model->getEntryPointAPIs($id);
        foreach($rows as $row){
            $this->api_versions[] = $row['api_id'];
        }
        return true;
    }
    public function set_HttpMethod($method){
        $method = $this->model->getHttpMethods($method);
        if (count($method)>0){
            $this->http_method = $method;
            return $this->http_method;
        }else{
            return false;
        }
    }
    public function get_URLParams(){
        if (count($this->urlParams)>0){
            return $this->urlParams;
        }else{
            if (count($this->params)>0){
                $this->urlParams = array();
                $x=0;
                foreach ($this->params as $param){
                    if ($param->url==true){
                        $this->urlParams[] = $param;
                        $x++;
                    }
                }
                if ($x>0){
                    return $this->urlParams;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    public function get_RequestParams(){
        if (count($this->requestParams)>0){
            return $this->requestParams;
        }else{
            if (count($this->params)>0){
                $this->requestParams = array();
                $x=0;
                foreach ($this->params as $param){
                    if ($param->url==false){
                        $this->requestParams[] = $param;
                        $x++;
                    }
                }
                if ($x>0){
                    return $this->requestParams;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }

    public function set_ApiVersions(array $versions){
        $this->api_versions = $versions;
    }
} 