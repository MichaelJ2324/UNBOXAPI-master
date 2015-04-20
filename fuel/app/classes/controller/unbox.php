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
        $OAuthClient = new \Oauth\Client();
        $OAuthClient->validateToken();
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