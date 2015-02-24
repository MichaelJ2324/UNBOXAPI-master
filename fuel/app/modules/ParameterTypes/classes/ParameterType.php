<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 1/26/15
 * Time: 9:06 AM
 */

namespace ParameterTypes;


class ParameterType extends \UNBOXAPI\Module{

    protected static $_name = "ParameterTypes";
    protected static $_label = "Parameter Type";
    protected static $_label_plural = "Parameter Types";
    protected static $_type = "Module";
    protected static $_enabled = true;

    private $defaults = array();
    private $data_types = array();
    private $api_types = array();
    private $templates = array();
    private $types = array(
        1 => 'data',
        2 => 'api'
    );

    public $id;
    public $name;
    public $type;
    public $template;


    function __construct($type){

        $this->defaults = $this->getDefaults();
        $this->loadDataTypes();
        $this->loadApiTypes();
        $this->loadTemplates();
        $this->set_type($type);
    }
    public static function get($type=""){
        $pt = Model\ParameterTypes::findType($type);
        return static::formatResult($pt);
    }
    public static function filter(){

    }

    private function getDefaults(){
        $module = static::$_name;
        $defaults = \Config::load("$module::baseTypes",$module,true);
        if ($defaults === false){
            $defaults = \Config::get("$module::baseTypes");
        }
        return $defaults;
    }
    private function loadDataTypes(){
        $data_types = array();
        $this->data_types = $this->defaults['data_types'];
        $data_types = include(APPPATH."custom/modules/ParameterTypes/data_types.php");
        if (count($data_types)>0){
            $this->data_types = array_merge($this->data_types,$data_types);
        }
        return true;
    }
    private function loadApiTypes(){
        $api_types = array();
        $this->api_types = $this->defaults['api_types'];
        $api_types = include(APPPATH."custom/modules/ParameterTypes/api_types.php");
        if (count($api_types)>0){
            $this->api_types = array_merge($this->api_types,$api_types);
        }
        return true;
    }
    private function loadTemplates(){
        $templates = array();
        $this->templates = $this->defaults['templates'];
        $templates = include(APPPATH."custom/modules/ParameterTypes/templates.php");
        if (count($templates)>0){
            $this->templates = array_merge($this->templates,$templates);
        }
        return true;
    }
    public function set_type($type){
        if (in_array($type,$this->data_types)){
            $this->type = 1;
            $this->name = $type;
            $this->set_template($type);
            return $type;
        }else if (in_array($type, $this->api_types)) {
            $this->type = 2;
            $this->name = $type;
            $this->set_template($type);
            return $type;
        }
        return false;
    }
    private function set_template($type){
        if (array_key_exists($type,$this->templates)){
            $this->template = $this->templates[$type];
            return $this->template;
        }
        return false;
    }
}