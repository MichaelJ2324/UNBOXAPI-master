<?php

namespace Controller;

class Unbox extends \Controller
{
    /**
     * The basic welcome message
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        $loggedIn = false;
        try{
            $OAuthClient = new \Oauth\Client();
            $loggedIn = $OAuthClient->validateAuth();
        }catch(\Exception $ex){
            \Log::info("OAuth Exception: [".$ex->getCode()."]".$ex->getMessage());
        }
        $data = array(
            'user' => "null"
        );
        if ($loggedIn){
            $data['user'] = $OAuthClient->getUserId();
        }
        return \Response::forge(\View::forge('index',$data));
    }
    /**
     * The 404 action for the application.
     *
     * @access  public
     * @return  Response
     */
    public function action_404()
    {
        return \Response::forge(\ViewModel::forge('UNBOXAPI/404'), 404);
    }
}