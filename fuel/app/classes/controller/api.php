<?php

namespace Controller;


class Api extends \Controller_Rest{


    public $oauth_client;

    private function loggedIn(){
        try{
            $loggedIn = $this->oauth_client->validateAuth();
            return $loggedIn;
        }catch(\Exception $ex){
            \Log::debug("Exception:".$ex->getMessage());
            $this->logged_in = false;
            return false;
        }
    }

    public function router($resource, $arguments) {
        try{
            $this->oauth_client = new \Oauth\Client();
            if (!$this->loggedIn()&&$resource!=="metadata")
            {
                $response = \Response::forge(\Format::forge(array('Error' => 'Invalid Access Token'))->to_json(), 401)->set_header('Content-Type', 'application.json');
                return $response;
            }
            parent::router($resource, $arguments);
        }catch(\Exception $ex){
            $response = \Response::forge(\Format::forge(array('Error' => $ex->getMessage()))->to_json(), 500)->set_header('Content-Type', 'application.json');
            return $response;
        }
    }

    /******************************************
     *
     * ENTRY POINT for Metadata Handling
     *
     ******************************************/
    /**
     * Login is not required, metadata is always returned.
     * @param string $module
     * @return mixed
     */

    public function get_metadata($module="")
    {
        try {
            $response = "";
            if ($module == "" || !isset($module)) {
                $response = \UNBOXAPI\Metadata::get_metaData();
            } else {
                if (\Module::exists($module)!==false){
                    if (substr($module, -1) === "s"){
                        $class = substr($module,0,-1);
                    }else{
                        $class = $module;
                    }
                    $Class = "\\$module\\$class";
                    $response = $Class::metadata();
                }else{
                    if ($module=='Config'){
                        $response = \UNBOXAPI\Metadata::get_config();
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
    /********************************************
     * ******************************************
     * REST API Setup for Modules
     *
     * GET Methods
     *  /   api     /    [module]    /   { [id] || "filter" }   /    { "link" || "related" }    /   [module]    /   { [related_id] || "filter" }
     *
     * POST,PUT & DELETE
     *  /   api /   [module]   /  [id]  /   link    /   [module]    /   [related_id]
     *
     * ********************************************
     * ********************************************/
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
                if ($id=="") {
                    $response = \Applications\Application::get();
                }else{
                    if ($id=='filter'){
                        $response = \Applications\Application::filter();
                    }else{
                        $response = \Applications\Application::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Applications\Application::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \Applications\Application::filterRelated($id,$related_module);
                        }else{
                            $response = \Applications\Application::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function post_applications($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Applications\Application::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Applications\Application::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                $response = \Applications\Application::update($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Applications\Application::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                $response = \Applications\Application::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Applications\Application::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
     * ENTRY POINTS for Apis
     *
     ******************************************/
    public function get_apis($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Apis\Api::get();
                }else{
                    if ($id=='filter'){
                        $response = \Apis\Api::filter();
                    }else{
                        $response = \Apis\Api::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Apis\Api::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \Apis\Api::filterRelated($id,$related_module);
                        }else{
                            $response = \Apis\Api::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function post_apis($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Apis\Api::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Apis\Api::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function put_apis($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Apis\Api::update($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Apis\Api::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                $response = \Apis\Api::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Apis\Api::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                }else{
                    if ($id=='filter'){
                        $response = \HttpMethods\HttpMethod::filter();
                    }else{
                        $response = \HttpMethods\HttpMethod::get($id);
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
    public function get_entryPoints($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \EntryPoints\EntryPoint::get();
                }else{
                    if ($id=='filter'){
                        $response = \EntryPoints\EntryPoint::filter();
                    }else{
                        $response = \EntryPoints\EntryPoint::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \EntryPoints\EntryPoint::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \EntryPoints\EntryPoint::filterRelated($id,$related_module);
                        }else{
                            $response = \EntryPoints\EntryPoint::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                if ($id=="") {
                    $response = \EntryPoints\EntryPoint::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \EntryPoints\EntryPoint::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                switch ($action) {
                    case "link":
                        $response = \EntryPoints\EntryPoint::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                $response = \EntryPoints\EntryPoint::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \EntryPoints\EntryPoint::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function get_parameters($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Parameters\Parameter::get();
                }else{
                    if ($id=='filter'){
                        $response = \Parameters\Parameter::filter();
                    }else{
                        $response = \Parameters\Parameter::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Parameters\Parameter::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \Parameters\Parameter::filterRelated($id,$related_module);
                        }else{
                            $response = \Parameters\Parameter::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                if ($id=="") {
                    $response = \Parameters\Parameter::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Parameters\Parameter::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
                switch ($action) {
                    case "link":
                        $response = \Parameters\Parameter::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function delete_parameters($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Parameters\Parameter::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Parameters\Parameter::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function get_parameterTypes($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \ParameterTypes\ParameterType::get();
                }else{
                    if ($id=='filter'){
                        $response = \ParameterTypes\ParameterType::filter();
                    }else{
                        $response = \ParameterTypes\ParameterType::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \ParameterTypes\ParameterType::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \ParameterTypes\ParameterType::filterRelated($id,$related_module);
                        }else{
                            $response = \ParameterTypes\ParameterType::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function post_parameterTypes($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \ParameterTypes\ParameterType::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \ParameterTypes\ParameterType::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function put_parameterTypes($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \ParameterTypes\ParameterType::update($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \ParameterTypes\ParameterType::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function delete_parameterTypes($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \ParameterTypes\ParameterType::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \ParameterTypes\ParameterType::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
     * ENTRYPOINTS for Logins
     *
     ****************************************/
    public function get_logins($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Logins\Login::get();
                }else{
                    if ($id=='filter'){
                        $response = \Logins\Login::filter();
                    }else{
                        $response = \Logins\Login::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Logins\Login::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \Logins\Login::filterRelated($id,$related_module);
                        }else{
                            $response = \Logins\Login::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function post_logins($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Logins\Login::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Logins\Login::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function put_logins($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Logins\Login::update($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Logins\Login::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function delete_logins($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Logins\Login::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Logins\Login::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
     * ENTRYPOINTS for Tests
     *
     ****************************************/
    public function get_tests($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Tests\Test::get();
                }else{
                    if ($id=='filter'){
                        $response = \Tests\Test::filter();
                    }else{
                        $response = \Tests\Test::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Tests\Test::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = \Tests\Test::filterRelated($id,$related_module);
                        }else{
                            $response = \Tests\Test::related($id,$related_module,$related_id);
                        }
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function post_tests($id="",$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = \Tests\Test::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = \Tests\Test::createRelated($id,$related_module);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function put_tests($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Tests\Test::update($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Tests\Test::updateRelated($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
    public function delete_tests($id,$action="",$related_module="",$related_id=""){
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = \Tests\Test::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = \Tests\Test::deleteRelationship($id,$related_module,$related_id);
                        break;
                    default:
                        throw new \Exception("Unknown action provided for parameter 3 of request");
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
     * ENTRYPOINTS for Layouts
     *
     ****************************************/
    /**
     * This function will return the public facing saved Test for tester. Login is not required
     * @param $test_id
     * @return mixed
     */
    public function get_tester($test_id){
        try
        {
            $response = "";
            $response = \Tester\Tester::get($test_id);
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


    public function post_tester($test_id=""){
        try
        {
            $response = "";
            $response = \Tester\Tester::run($test_id);
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



}