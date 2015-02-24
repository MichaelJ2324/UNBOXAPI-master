<?php

namespace Controller;

use EntryPoints\Model\EntryPoints;
use Oil\Exception;

class Api extends \Controller_Rest{
    /******************************************
     *
     * ENTRY POINTS for System Stuff
     *
     ******************************************/
    /**
     * @param string $module
     * @return mixed
     */
    public function get_metadata($module="")
    {
        try {
            $response = "";
            if ($module == "" || !isset($module)) {
                $response = \UNBOXAPI\Unbox::get_metaData();
            } else {
                if (\Module::exists($module)!==false){
                    if (substr($module, -1) === "s"){
                        $class = substr($module,0,-1);
                    }else{
                        $class = $module;
                    }
                    $Class = "\\$module\\$class";
                    $response = $Class::get_metaData();
                }else{
                    if ($module=='Config'){
                        $response = \UNBOXAPI\Unbox::get_config();
                    }else{
                        throw new \Exception("Module does not exist",500);
                    }
                }
            }
            return $this->response(
                $response
            );
        } catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: " . $e->getMessage() . "\n",
                )
            );
        }
    }
    /******************************************
     *
     * ENTRY POINTS for Applications
     *
     ******************************************/
    public function get_applications($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Applications\Application::get($id);
            }else{
                switch ($action){
                    case 'apis':
                        $response = \Applications\Application::apis($id);
                        break;
                    case 'entryPoints':
                        $response = \Applications\Application::entryPoints($id);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                )
            );
        }
    }
    public function post_applications($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Applications\Application::create();
            }else{
                switch ($action){
                    case 'link':
                        $response = \Applications\Application::relate($id,$related_id);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function put_applications($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id!==""||isset($id)) {
                    $response = \Applications\Application::update($id);
                }
                else{
                    throw new \Exception("Missing ID ".$id,500);
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function delete_applications($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=='filter'){

                }else {
                    if ($id=="") {

                    }else{

                    }
                }
            }else{
                switch ($action){

                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    /******************************************
     *
     * ENTRY POINTS for APIs
     *
     ******************************************/
    public function get_apis($id="",$related="",$filter=""){
        try
        {
            $response = "";
            if ($related==""||!isset($related)){
                $response = \APIs\API::get($id);
            }else{
                switch ($related){
                    case 'methods':
                        $response = \APIs\API::methods($id);
                        break;
                    case 'logins':
                        $response = \APIs\API::logins($id);
                        break;
                    case 'entryPoints':
                        $response = \APIs\API::entryPoints($id,$filter);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                )
            );
        }
    }
    public function post_apis($id="",$action="",$entryPoint=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \APIs\API::create();
            }else{
                switch ($action){
                    case 'login':
                        $response = \APIs\API::login($id,$entryPoint);
                        break;
                    case 'test':
                        $response = \APIs\API::test($id,$entryPoint);
                        break;
                    case 'script':
                        $response = \APIs\API::buildScript($id,$entryPoint);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                )
            );
        }
    }
    public function put_apis($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id!==""||isset($id)) {
                    $response = \APIs\API::update($id);
                }
                else{
                    throw new \Exception("Missing ID");
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function delete_apis($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id!==""||isset($id)) {
                    $response = \Applications\Application::update($id);
                }
                else{
                    throw new \Exception("Missing ID ".$id,500);
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }

    /******************************************
     *
     * ENTRY POINTS for HttpMethods
     *
     ******************************************/
    public function get_httpMethods($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \HttpMethods\HttpMethod::get();
                }
            }else{
                switch ($action){
                    case 'params':
                        $response = \EntryPoints\EntryPoint::getParams($id);
                        break;
                    case 'urlParams':
                        break;
                    case 'requestParams':
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n"
                ),
                400
            );
        }
    }
    /******************************************
     *
     * ENTRY POINTS for EntryPoints
     *
     ******************************************/
    /***
     * @param string $id
     * @param string $action
     * @param string $related_module
     * @param string $related_id
     * @return mixed
     */
    public function get_entryPoints($id="",$action="",$relationship="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=='filter'){
                    $response = \EntryPoints\EntryPoint::filter();
                }else {
                    $response = \EntryPoints\EntryPoint::get($id);
                }
            }else{
                switch ($action){
                    case 'link':
                        $response = \EntryPoints\EntryPoint::related($id,$relationship);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function post_entryPoints($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \EntryPoints\EntryPoint::create();
            }else{
                switch ($action){
                    case "params":
                        $response = \EntryPoints\EntryPoint::getParams($id);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function put_entryPoints($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \EntryPoints\EntryPoint::update($id);
            }else{
                switch ($action){

                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function delete_entryPoints($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \EntryPoints\EntryPoint::delete();
            }else{
                switch ($action){

                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    /******************************************
     *
     * ENTRY POINTS for Parameters
     *
     ******************************************/
    public function get_parameters($id="",$action="",$relationship="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Parameters\Parameter::get($id);
            }else{
                switch ($action){
                    case 'link':
                        $response = \Parameters\Parameter::related($id,$relationship);
                        break;
                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function post_parameters($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Parameters\Parameter::create();
            }else{
                switch ($action){

                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function put_parameters($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Parameters\Parameter::update($id);
            }else{
                switch ($action){

                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }
    public function delete_parameters($id, $action=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=='filter'){

                }else {
                    if ($id=="") {

                    }else{

                    }
                }
            }else{
                switch ($action){

                }
            }
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }

    /*****************************************
     *
     * ENTRYPOINTS for ParameterTypes
     *
     ****************************************/
    public function get_parameterTypes($type=""){
        try
        {
            $response = \ParameterTypes\ParameterType::get($type);
            return $this->response(
                $response
            );
        }
        catch (\Exception $e) {
            return $this->response(
                array(
                    'err' => 'true',
                    'msg' => "Caught exception: ".$e->getMessage()."\n",
                ),
                400
            );
        }
    }

    public function get_modal($window){
        $view = \View::forge("UNBOXAPI/$window")->render();
        $data = array(
            'head' => 'UNBOXAPI: '.ucfirst($window),
            'body' => $view,
            'foot' => '<button class="btn btn-primary" data-dismiss="modal">OK</button>'
        );
        return array(
            'err' => false,
            'msg' => "Modal $window Loaded.",
            'data' => $data
        );
    }
}