<?php

namespace Controller;

class Unbox extends \Controller
{
    private $data = array(
        'bootstrapped_data' => ""
    );
    /**
     * The basic welcome message
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        return \Response::forge(\View::forge('index',$this->data));
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