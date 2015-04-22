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
        try{
            $OAuthClient = new \Oauth\Client();
            $OAuthClient->validateToken();
        }catch(\Exception $ex){
            \Log::info("OAuth Exception: [".$ex->getCode()."]".$ex->getMessage());
        }
        return \Response::forge(\View::forge('index'));
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