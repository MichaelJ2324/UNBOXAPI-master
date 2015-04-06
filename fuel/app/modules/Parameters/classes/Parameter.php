<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 7/8/14
 * Time: 12:31 AM
 */

namespace Parameters;

class Parameter extends \UNBOXAPI\Module{

    protected static $_name = "Parameters";
    protected static $_label = "Parameter";
    protected static $_label_plural = "Parameters";
    protected static $_enabled = true;

    public $data_type;
    public $api_type;
    public $description;
    public $html;
    public $required;
    public $order;
    public $login_pane;
    public $url;
    public $related_entryPoint;

    function __construct($entryPoint,$param_id=""){
        $this->model = new Model\Parameters();
        $this->related_entryPoint = $entryPoint;
        if (isset($param_id)&&$param_id!==""){
            if($this->retrieve_Param($entryPoint,$param_id)===false){
                return false;
            }
        }
    }

    private function retrieve_Param($entryPoint,$param_id){
        $param = $this->model->getEntryPointParam($param_id,$entryPoint);
        if (count($param)==1){
            foreach($param as $row){
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->description = $row['description'];
                $this->order = $row['order'];
                $this->required = ($row['required']==1?true:false);
                $this->login_pane = $row['login_pane'];
                $this->url = ($row['url_param']==1?true:false);
                $type = new ParamType($row['data_type'],$row['api_type']);
                $this->data_type = $type->type;
                $this->api_type = $type->api_type;
                $this->set_html($type);
            }
            return true;
        }else{
            return false;
        }
    }

    public function set_html(ParamType $type){
        if (isset($type)&&is_object($type)){
            $html_format = $type->get_html_format();
            if($html_format['method']=='function'){
                $this->html = $this->$html_format['data']();
            }else{
                $this->html = $html_format['data'];
            }
            $this->html = str_replace("<name>",$this->name,$this->html);
            if ($this->required==true){
                $this->html = str_replace("<required>","required='true'",$this->html);
            }else{
                $this->html = str_replace("<required>","",$this->html);
            }
        }else{
            return false;
        }
    }

    public function create_Record_Field(){
        return "<textarea id='<name>' name='<name>' class='form-control'></textarea>";
    }
} 