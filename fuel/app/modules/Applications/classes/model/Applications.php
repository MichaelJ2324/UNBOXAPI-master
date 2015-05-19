<?php

namespace Applications\Model;

class Applications extends \Model\Module{
    private $db_conn = "default";
    protected static $_table_name = 'applications';
    protected static $_fields = array(
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'validation' => array(
                'max_length' => 2048
            ),
            'form' => array(
                'type' => 'textarea'
            )
        ),
        'version_id' => array(
            'data_type' => 'varchar',
            'label' => 'Version ID',
            'validation' => array(
                'max_length' => 50
            ),
            'form' => false,
        ),
    );
    protected static $_relationships = array(
        'has_one' => array(
            'version' => array(
                'key_from' => 'version_id',
                'model_to' => "Versions\\Model\\Applications",
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),
        'has_many' => array(
            'apis' => array(
                'key_from' => 'id',
                'model_to' => "Applications\\Model\\Apis",
                'key_to' => 'application_id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        ),

    );

    public function getApplication($id="")
    {
        $query = \DB::select("A.id","A.name","A.version","A.description",array(\DB::expr("CONCAT(A.name,' - (',A.version,')')"),"value"))->from(array('applications','A'));
        if ($id!=""){
            $query->where("A.id",$id);
        }else{
            if (\Input::param("limit")) {
                $query->limit(\Input::param("limit"));
            }
            if (\Input::param("offset")){
                $query->offset(\Input::param("offset"));
            }
        }
        return $query->execute($this->db_conn)->as_array();
    }
    public function getAPIs($id)
    {
        $query = \DB::select('API.id','API.name','API.version',array(\DB::expr("CONCAT(API.name,' - (',API.version,')')"),"value"))->from(array('applications','A'));
        $query->join(array('application_apis','AA'),'INNER')->on('AA.application_id','=','A.id');
        $query->join(array('apis','API'),'INNER')->on('AA.api_id','=','API.id');
        $query->where('A.id',$id);
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute($this->db_conn)->as_array();
    }
    public function getEntrypoints($id)
    {
        $query = \DB::select('EP.id','EP.name','EP.url','EP.method',array('HM.method','method_name'),'EP.description',array(\DB::expr("CONCAT(EP.name,' [',EP.url,']')"),"value"))->from(array('applications','A'));
        $query->join(array('application_apis','AA'),'INNER')->on('AA.application_id','=','A.id');
        $query->join(array('apis','API'),'INNER')->on('AA.api_id','=','API.id');
        $query->join(array("api_entrypoints","AEP"),"INNER")->on("API.id","=","AEP.api_id");
        $query->join(array("entry_points","EP"),"INNER")->on("EP.id","=","AEP.entry_point_id");
        $query->join(array('http_methods','HM'),'INNER')->on('EP.method','=','HM.id');
        $query->where('A.id',$id);
        $query->distinct();
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute($this->db_conn)->as_array();
    }
} 