<?php

namespace Applications\Model;

class Applications extends \Model\Unbox{
    private $db_conn = "default";
    protected static $_table_name = 'applications';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'smallint',
            'label' => 'App ID',
            'null' => false,
            'auto_inc' => true
        ),
        'name' => array(
            'data_type' => 'varchar',
            'label' => 'Name',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(3),
                'max_length' => array(50)
            ),
            'form' => array('type' => 'text'),
        ),
        'version' => array(
            'data_type' => 'varchar',
            'label' => 'Version',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => array(10)
            ),
            'form' => array('type' => 'text'),
        ),
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'max_length' => array(2048)
            ),
            'form' => array(
                'type' => 'textarea'
            )
        ),
        'deleted' => array(
            'data_type' => 'tinyint',
            'label' => 'Description',
            'default' => 0,
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => array(500)
            ),
            'form' => false
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Modified',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => array(
                'type' => 'text',
                'disabled' => 'disabled'
            ),
        ),
    );
    protected static $_many_many = array(
        'apis' => array(
            'key_from' => 'id',
            'key_through_from' => 'application_id', // column 1 from the table in between, should match a posts.id
            'table_through' => 'application_apis', // both models plural without prefix in alphabetical order
            'key_through_to' => 'api_id', // column 2 from the table in between, should match a users.id
            'model_to' => "APIs\\Model\\APIs",
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'property' => 'date_created',
            'overwrite' => false,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
            'property' => 'date_modified',
            'relations' => array(
                'apis'
            ),
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
    public function getEntryPoints($id)
    {
        $query = \DB::select('EP.id','EP.name','EP.url','EP.method',array('HM.method','method_name'),'EP.description',array(\DB::expr("CONCAT(EP.name,' [',EP.url,']')"),"value"))->from(array('applications','A'));
        $query->join(array('application_apis','AA'),'INNER')->on('AA.application_id','=','A.id');
        $query->join(array('apis','API'),'INNER')->on('AA.api_id','=','API.id');
        $query->join(array("api_entryPoints","AEP"),"INNER")->on("API.id","=","AEP.api_id");
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