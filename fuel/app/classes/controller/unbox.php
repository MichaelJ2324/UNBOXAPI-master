<?php

namespace Controller;

class Unbox extends \Controller
{
	protected $auth = "authorize";
	protected $authorization = true;
	protected $auth_required = false;
	protected $scope = '';

	protected $oauth_client;
	protected $authorized = FALSE;

	protected function authorize(){
		try{
			if ($this->authorization) {
				$this->oauth_client = \OAuth\Client::getInstance();
				$token = \OAuth\Client::getCookie();
				if ($token !== false) {
					if ($this->oauth_client->validateAuth($token,$this->scope)) {
						$this->authorized = TRUE;
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

    /**
     * The main application
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        $data = array(
            'user' => null
        );
        if ($this->authorized){
			$user_info = $this->oauth_client->getUserInfo();
            $data['user'] = $user_info['user_id'];
        }

        return \Response::forge(\View::forge('index',$data));
    }
	public function action_login(){

	}
    /**
     * The 404 action for the application.
     *
     * @access  public
     * @return  Response
     */
    public function action_404()
    {
        return \Response::forge(\View::forge('404'),404);
    }
}