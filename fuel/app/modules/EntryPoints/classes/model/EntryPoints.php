<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 7/4/14
 * Time: 10:03 PM
 */
namespace EntryPoints\Model;

class EntryPoints extends \Model\Module{

    protected static $_table_name = 'entry_points';
    protected static $_fields = array(
        'method' => array(
            'data_type' => 'tinyint',
            'label' => 'HTTP Method ID',
            'null' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'relate',
                'name' => 'method',
                'module' => 'HttpMethods'
            ),
            'filter' => true
        ),
        'url' => array(
            'data_type' => 'varchar',
            'label' => 'URL',
            'null' => false,
            'validation' => array(
                'required',
                'max_length' => 250
            ),
            'form' => array(
                'type' => 'text',
                'name' => 'url',
            ),
        ),
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'null' => true,
            'validation' => array(
                'max_length' => 2048
            ),
            'form' => array(
                'type' => 'textarea'
            ),
        ),
        'version_id' => array(
            'data_type' => 'varchar',
            'label' => 'Version ID',
            'validation' => array(
                'max_length' => 50
            ),
            'form' => false,
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'default' => 0,
            'validation' => array(
                'max_length' => 0
            ),
            'form' => false,
        ),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'login_entryPoint' => array(
                'key_from' => 'id',
                'model_to' => 'Logins\\Model\\Logins',
                'key_to' => 'login_entryPoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'logout_entryPoint' => array(
                'key_from' => 'id',
                'model_to' => 'Logins\\Model\\Logins',
                'key_to' => 'logout_entryPoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'has_one' => array(
            'httpMethod' => array(
                'key_from' => 'method',
                'model_to' => 'HttpMethods\\Model\\HttpMethods',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'version' => array(
                'key_from' => 'version_id',
                'model_to' => 'Versions\\Model\\EntryPoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
        'has_many' => array(
            'parameters' => array(
                'key_from' => 'id',
                'model_to' => 'EntryPoints\\Model\\Parameters',
                'key_to' => 'entryPoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'apis' => array(
                'key_from' => 'id',
                'model_to' => 'Apis\\Model\\EntryPoints',
                'key_to' => 'entryPoint_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),
    );

    public function filterEntryPoints($data=""){
        $query = \DB::select('EP.id','EP.name','EP.url','EP.method',array('HM.method','method_name'),'EP.description',array(\DB::expr("CONCAT(EP.name,' [',EP.url,']')"),"value"))->from(array('applications','A'));
        $query->join(array('application_apis','AA'),'INNER')->on('AA.application_id','=','A.id');
        $query->join(array('apis','API'),'INNER')->on('AA.api_id','=','API.id');
        $query->join(array("api_entryPoints","AEP"),"INNER")->on("API.id","=","AEP.api_id");
        $query->join(array("entry_points","EP"),"INNER")->on("EP.id","=","AEP.entry_point_id");
        $query->join(array('http_methods','HM'),'INNER')->on('EP.method','=','HM.id');
        $query->distinct();
        $query->where("EP.id","!=","");
        foreach($data as $parameter=>$value){
            if (array_key_exists($parameter,$this->columns)){
                if ($this->columns[$parameter]['type']=='varchar') {
                    $query->and_where("EP." . $parameter, "LIKE", $value . "%");
                }else{
                    $query->and_where("EP." . $parameter,$value);
                }
            }else{
                if ($parameter=="api"){
                    $query->and_where("API.id",$value);
                }else if ($parameter=="application"){
                    $query->and_where("A.id",$value);
                }
            }
        }
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute(self::$_connection)->as_array();
    }
    public function getEntryPoint($id=""){
        $query = \DB::select('EP.id','EP.name','EP.url','EP.method',array('HM.method','method_name'),'EP.description')->from(array('entry_points','EP'));
        $query->join(array('http_methods','HM'),'INNER')->on('EP.method','=','HM.id');
        if ($id!=""){
            $query->where('EP.id',$id);
        }
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute(self::$_connection)->as_array();
    }
    public static function getParameters($id){
        $query = \DB::select('P.id',array('PTD.name','data_type_name'),array('PTA.name','api_type_name'),'P.name','P.description','P.url_param','EPP.required','EPP.order','EPP.login_pane')->from(array('parameters','P'));
        $query->join(array('entryPoint_parameters','EPP'),'INNER')->on('P.id','=','EPP.parameter_id');
        $query->join(array('parameter_types','PTD'),'INNER')->on('P.data_type','=','PTD.id');
        $query->join(array('parameter_types','PTA'),'LEFT OUTER')->on('P.api_type','=','PTA.id');
        $query->where('EPP.entryPoint_id',$id);
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute(self::$_connection)->as_array();
    }
    public function getEntryPointAPIs($id){
        $query = \DB::select('AEP.api_id')->from(array('entry_points','EP'));
        $query->join(array('api_entryPoints','AEP'),'INNER')->on('EP.id','=','AEP.entryPoint_id');
        $query->where('EP.id',$id);
        return $query->execute(self::$_connection)->as_array();
    }
} 