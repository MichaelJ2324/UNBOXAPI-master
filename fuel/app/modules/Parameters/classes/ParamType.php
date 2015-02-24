<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 6/30/14
 * Time: 11:55 PM
 */

namespace Parameters;


class ParamType {

    private $types = array(
        'string',
        'integer',
        'boolean',
        'array',
    );
    private $api_types = array(
        'module',
        'record_id',
        'report_id',
        'filter',
        'date',
        'grant_type',
        'client_id',
        'platform',
        'record',
        'password'
    );
    private $paramType_handlers = array(
        'string' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<input type='text' name='<name>' id='<name>' value='' placeholder='<name>' class='form-control' <required> />"
            ),
        ),
        'integer' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<input type='text' name='<name>' id='<name>' value='' placeholder='<name>' class='form-control' <required> />"
            ),
        ),
        'boolean' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<input id='<name>_hidden'  type='hidden' value='0' name='<name>'>
                            <input type='checkbox' name='<name>' id='<name>' value='1' placeholder='<name>' class='form-control' <required> />"
            ),
        ),
        'array' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<textarea name='<name>' id='<name>' placeholder='<name>' class='form-control' <required> ></textarea>"
            ),
        ),
        'date' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<input type='text' name='<name>' id='<name>' value='' placeholder='<name>' class='form-control datepicker' <required> />"
            ),
        ),
        'password' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<input type='password' name='<name>' id='<name>' value='' placeholder='<name>' class='form-control' <required> />"
            ),
        ),
        'module' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<input type='text' name='<name>' id='<name>' value='' placeholder='<name>' class='form-control' <required> />"
            ),
        ),
        'record_id' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<div class='input-group'>
                            <span class='input-group-btn'>
                                <button class='btn btn-default' type='button'>
                                    <span class='glyphicon glyphicon-search'></span>
                                </button>
                            </span>
                            <input type='text' class='form-control' placeholder='<name>' id='<name>' name='<name>' <required> />
                        </div>"
            ),
        ),
        'report_id' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<div class='input-group'>
                            <span class='input-group-btn'>
                                <button class='btn btn-default' type='button'>
                                    <span class='glyphicon glyphicon-search'></span>
                                </button>
                            </span>
                            <input type='text' class='form-control' placeholder='<name>' id='<name>' name='<name>' <required> />
                        </div>"
            ),
        ),
        'grant_type' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<select id='<name>' name='<name>' class='form-control select2' <required> >
                            <option value='password'>Password</option>
                            <option value='refresh_token'>Refresh Token</option>
                        </select>"
            ),
        ),
        'client_id' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<select id='<name>' name='<name>' class='form-control select2' <required> >
                            <option value='sugar'>sugar</option>
                            <option value='support_portal'></option>
                        </select>"
            ),
        ),
        'platform' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<select id='<name>' name='<name>' class='form-control select2' <required> >
                            <option value='base'>Base</option>
                            <option value='mobile'>Mobile</option>
                            <option value='portal'>Portal</option>
                            <option value='api'>API</option>
                        </select>"
            ),
        ),
        'filter' => array(
            'html' => array(
                'method' => 'text',
                'data' => "<textarea id='<name>' name='<name>' class='form-control'></textarea>"
            ),
        ),
        'record' => array(
            'html' => array(
                'method' => 'function',
                'data' => 'create_Record_Field'
            ),
        ),
    );

    public $type;
    public $api_type;
    private $html_format;

    function __construct($type,$api_type=""){
        $this->type = $this->set_type($type);
        $this->api_type = $this->set_api_type($api_type);
    }
    public function get_html_format(){
        return $this->html_format;
    }
    public function set_type($type){
        if (in_array($type,$this->types)){
            $this->html_format = $this->paramType_handlers[$type]['html'];
            return $type;
        }else{
            return false;
        }
    }
    public function set_api_type($api_type){
        if (in_array($api_type,$this->api_types)){
            if ($api_type!=""){
                $this->html_format = $this->paramType_handlers[$api_type]['html'];
            }
            return $api_type;
        }else{
            return false;
        }
    }
} 