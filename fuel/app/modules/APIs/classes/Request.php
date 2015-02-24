<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 7/31/14
 * Time: 2:18 PM
 */

namespace APIs;


class Request {

    public $url;
    public $token;
    public $http_method = "GET";
    public $urlParams = array();
    public $requestParams = array();
    public $encodeData = true;
    public $returnHeaders = false;
    private $response;

    function __construct($url,$http_method="GET",$encodeData=true,$returnHeaders=true){
        $this->url = $url;
        $this->http_method = strtoupper($http_method);
        $this->encodeData = $encodeData;
        $this->returnHeaders = $returnHeaders;
    }
    public function set_EntryPoint(\EntryPoints\EntryPoint $entryPoint,$data=array()){
        $this->url = rtrim($this->url,"/") ."/". ltrim($entryPoint->url,"/");
        $this->http_method = $entryPoint->method_name;
        $urlParams = $entryPoint->get_URLParams();
        $requestParams = $entryPoint->get_RequestParams();
        if ($urlParams!==false){
            $this->loadURLParams($urlParams,$data);
        }
        if ($requestParams!==false){
            $this->loadRequestParams($requestParams,$data);
        }
    }
    private function loadURLParams($params,$data){
        foreach($params as $param){
            $this->urlParams[$param->name] = $data[$param->name];
            $this->url = str_replace($param->name,$data[$param->name],$this->url);
        }
    }
    public function loadRequestParams($params,$data){
        foreach($params as $param){
            if ($param->type=='array'){
                $lines = explode(";",$data[$param->name]);
                foreach($lines as $line){
                    if (strpos($line,"=")!==false) {
                        $key_value_pair = explode("=", $line);
                        $this->requestParams[$key_value_pair[0]] = $key_value_pair[1];
                    }
                }
            }else{
                $this->requestParams[$param->name] = $data[$param->name];
            }
        }
    }
    public function set_Data($data){

    }
    public function send(){
        if ($this->http_method == 'GET'){
            $this->url .= "?" . http_build_query($this->requestParams);
        }

        $curl_request = curl_init($this->url);

        if ($this->http_method == 'POST'){
            curl_setopt($curl_request, CURLOPT_POST, 1);
        }
        else {
            curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, $this->http_method);
        }
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, $this->returnHeaders);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

        if (!empty($this->token)){
            $token = array('Content-Type: application/json',"OAuth-Token: {$this->token}");
            curl_setopt($curl_request, CURLOPT_HTTPHEADER, $token);
        }

        if (!empty($this->requestParams) && $this->http_method !== 'GET')
        {
            $requestParams = $this->requestParams;
            if ($this->encodeData)
            {
                $requestParams = json_encode($this->requestParams);
            }
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $requestParams);
        }
        $result = curl_exec($curl_request);

        if ($this->returnHeaders)
        {
            //set headers from response
            list($headers, $content) = explode("\r\n\r\n", $result ,2);
            foreach (explode("\r\n",$headers) as $header)
            {
                header($header);
            }

            $result = trim($content);
        }

        $response = array();
        $request = curl_getinfo($curl_request);
        $request['body'] = (!empty($this->requestParams) && $this->http_method !== 'GET' ? $this->requestParams : "" );
        $response['request'] = json_encode($request);

        curl_close($curl_request);

        $response['response'] = $result;

        $this->response = $response;
        return $this->response;
    }
    function getResponse(){
        return $this->response;
    }
} 