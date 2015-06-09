<?php

namespace Controller\V1;


class Rest extends \Controller_Rest {

    protected $rest_format = "json";
    protected $auth = "authorize";
    protected $authorization = true;
    protected $auth_required = true;
	protected $scope = 'api';

    protected $oauth_client;
	protected $authorized = FALSE;
    protected $module;
    protected $class;

    protected function authorize(){
        try{
            if ($this->authorization) {
				$this->oauth_client = \OAuth\Client::getInstance();
				$token = \OAuth\Client::getCookie();
				if ($token !== false) {
					if ($this->oauth_client->validateAuth($token,$this->scope)) {
						\Log::debug("Authorized!");
						$this->authorized = TRUE;
					}
				}else{
					$token = \Input::headers('Authorization');
					if (!empty($token)){
						if ($token!==null){
							if (strpos($token,"Bearer") !== false) {
								$tokenArray = explode(" ", $token);
								$token = $tokenArray[1];
							}
						}else{
							$token = \Input::all('access_token');
						}
						if (!($token==null||$token==""||empty($token))){
							if ($this->oauth_client->validateAuth($token,$this->scope,false)) {
								$this->authorized = TRUE;
							}
						}
					}
				}
				if ($this->authorized===TRUE || $this->auth_required == FALSE) {
					return TRUE;
				}
				return FALSE;
            }else{
                return TRUE;
            }
        }catch(\Exception $ex){
            \Log::debug("Exception:".$ex->getMessage());
            if ($this->auth_required == false){
                return true;
            }
            return false;
        }
    }

    public function before(){
        if (!isset($this->class)){
            if (!isset($this->module)){
                $module = get_class($this);
                $module = explode("\\",$module);
                $this->module = end($module);
            }
            $class = \UNBOXAPI\Data\Util\Module::classify($this->module);
            $class = "\\" . $this->module . "\\" . $class;
            if (class_exists($class)) {
                $this->class = $class;
            }
        }
        parent::before();
    }
    public function get_index($id="",$action="",$related_module="",$related_id=""){
        $Class = $this->class;
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = $Class::get();
                }else{
                    if ($id=='filter'){
                        $response = $Class::filter();
                    }else{
                        $response = $Class::get($id);
                    }
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = $Class::recordRelated($id,$related_module,$related_id);
                        break;
                    case "related":
                        if ($related_id=='filter'){
                            $response = $Class::filterRelated($id,$related_module);
                        }else{
                            $response = $Class::related($id,$related_module,$related_id);
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
    public function post_index($id="",$action="",$related_module="",$related_id=""){
        $Class = $this->class;
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                if ($id=="") {
                    $response = $Class::create();
                }else{
                    throw new \Exception("Use PUT request for updating records.");
                }
            }else{
                switch ($action) {
                    case "link":
                        $response = $Class::createRelated($id,$related_module);
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
    public function put_index($id,$action="",$related_module="",$related_id=""){
        $Class = $this->class;
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = $Class::update($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = $Class::updateRelated($id,$related_module,$related_id);
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
    public function delete_index($id,$action="",$related_module="",$related_id=""){
        $Class = $this->class;
        try
        {
            $response = "";
            if ($action==""||!isset($action)){
                $response = $Class::delete($id);
            }else{
                switch ($action) {
                    case "link":
                        $response = $Class::unrelate($id,$related_module,$related_id);
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
}