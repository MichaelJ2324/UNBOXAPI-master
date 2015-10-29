<?php

namespace Entrypoints;

use UNBOXAPI\Box\Module;

class Entrypoint extends Module{

    protected static $_canisters = array(
        'Entrypoints',
        'Parameters'
    );

    public $description;
    public $url;
    public $method;
    public $method_name;
    public $api_versions = array();
    public $params = array();

    private $urlParams = array();
    private $requestParams = array();

    public static function getParams($id){
        $ep = new Entrypoint($id);
        return $ep->params;
    }

    private function retrieve_Entrypoint($id){
        $ep = $this->model->getEntrypoint($id);
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
        $rows = $this->model->getEntrypointAPIs($id);
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