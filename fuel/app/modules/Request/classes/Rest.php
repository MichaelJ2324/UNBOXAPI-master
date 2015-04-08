<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 4/7/15
 * Time: 6:20 PM
 */

namespace Request;


class Rest {

    public $url;
    public $http_method = "GET";
    public $headers = array();
    public $user_agent = 'UNBOX API';
    public $payload = array();
    public $encodeData = true;
    public $returnHeaders = true;
    private $_state = false;
    private $request;
    private $response;

    function __construct($url,$http_method="GET",$encodeData=true,$returnHeaders=true){
        $this->url = $url;
        $this->http_method = strtoupper($http_method);
        $this->encodeData = $encodeData;
        $this->returnHeaders = $returnHeaders;
    }
    public function getResponse(){
        return $this->response;
    }
    public function getRequest(){
        if ($this->_state==='done'){
            return false;
        }
        return $this->response;
    }
    public function send(){
        if ($this->_state==='init') {

            $this->response = curl_exec($this->request);
            if ($this->returnHeaders) {
                //set headers from response
                list($headers, $content) = explode("\r\n\r\n", $this->response, 2);
                foreach (explode("\r\n", $headers) as $header) {
                    header($header);
                }
                $this->response = trim($content);
            }
            $request = curl_getinfo($this->request);
            $request['body'] = (!empty($this->payload) && $this->http_method !== 'GET' ? $this->payload : "");
            curl_close($this->request);
            unset($this->request);
            $this->request = json_encode($request);
            $this->_state = 'done';
            return true;
        }else{
            return false;
        }
    }
    public function initialize(){
        $this->setupPayload();
        $this->request = curl_init($this->url);
        if ($this->http_method == 'POST'){
            curl_setopt($this->request, CURLOPT_POST, 1);
        }
        else {
            curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, $this->http_method);
        }
        curl_setopt($this->request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($this->request, CURLOPT_HEADER, $this->returnHeaders);
        curl_setopt($this->request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->request, CURLOPT_USERAGENT, $this->user_agent);

        if (!empty($this->headers)){
            curl_setopt($this->request, CURLOPT_HTTPHEADER, $this->headers);
        }

        if (!empty($this->payload) && $this->http_method !== 'GET')
        {
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $this->payload);
        }
        $this->_state = 'init';
    }
    private function setupPayload(){
        if ($this->http_method == 'GET'){
            $this->url .= "?" . http_build_query($this->payload);
        }else{
            if (!empty($this->payload)) {
                if ($this->encodeData) {
                    $this->payload = json_encode($this->payload);
                }
            }
        }
    }


}