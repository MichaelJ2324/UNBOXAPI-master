<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 7/4/14
 * Time: 11:32 PM
 */

namespace Apis\Model;

class Apis extends \Model\Module{
    private $db_conn = 'default';
    protected static $_table_name = 'apis';
    protected static $_fields = array(
        'url' => array(
            'data_type' => 'varchar',
            'label' => 'URL',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 250
            ),
            'form' => array('type' => 'text'),
        ),
        'login_required' => array(
            'data_type' => 'tinyint',
            'label' => 'Login Required?',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 1
            ),
            'form' => array('type' => 'checkbox'),
        ),
        'type' => array(
            'data_type' => 'varchar',
            'label' => 'Type',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 10
            ),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    0 => array(
                        'key'=>'REST',
                        'value' => 'REST'
                    ),
                    1 => array(
                        'key'=>'SOAP',
                        'value'=> 'SOAP'
                    )
                )
            )
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'default' => 0,
            'validation' => array(
                'max_length' => 0
            ),
            'form' => array(
                'type' => 'checkbox',
                'disabled' => 'disabled'
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
        'many_many' => array(
            'applications' => array(
                'key_from' => 'id',
                'key_through_from' => 'api_id', // column 1 from the table in between, should match a posts.id
                'table_through' => 'application_apis', // both models plural without prefix in alphabetical order
                'key_through_to' => 'appplication_id', // column 2 from the table in between, should match a users.id
                'model_to' => 'Applications\\Model\\Applications',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'entryPointVersion' => array(
                'key_from' => 'id',
                'key_through_from' => 'api_id', // column 1 from the table in between, should match a posts.id
                'table_through' => 'api_entryPoints', // both models plural without prefix in alphabetical order
                'key_through_to' => 'entryPoint_id', // column 2 from the table in between, should match a users.id
                'model_to' => 'EntryPoints\\Model\\EntryPoints',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'logins' => array(
                'key_from' => 'id',
                'key_through_from' => 'api_id', // column 1 from the table in between, should match a posts.id
                'table_through' => 'api_logins', // both models plural without prefix in alphabetical order
                'key_through_to' => 'login_id', // column 2 from the table in between, should match a users.id
                'model_to' => 'Logins\\Model\\Logins',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
            'httpMethods' => array(
                'key_from' => 'id',
                'key_through_from' => 'api_id', // column 1 from the table in between, should match a posts.id
                'table_through' => 'api_httpMethods', // both models plural without prefix in alphabetical order
                'key_through_to' => 'method_id', // column 2 from the table in between, should match a users.id
                'model_to' => 'HttpMethods\\Model\\HttpMethods',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            )
        )
    );

    public function getApi($id=""){
        $query = \DB::select('A.id','A.name','A.version',array(\DB::expr("CONCAT(A.name,' - (v',A.version,')')"),"value"),"A.url","A.login_required")->from(array('apis','A'));
        if ($id!==""){
            $query->where('A.id',$id);
        }
        return $query->execute($this->db_conn)->as_array();
    }
    public function getHttpMethods($id="")
    {
        $query = \DB::select('HM.id','HM.method',array("HM.method","value"))->from(array('http_methods','HM'));
        if ($id!=="") {
            $query->join(array("api_httpMethods", "AHM"), "INNER")->on("HM.id", "=", "AHM.method");
            $query->where('AHM.api_version', $id);
        }
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute($this->db_conn)->as_array();
    }
    public function getEntryPoints($id,$httpMethod="")
    {
        $query = \DB::select('EP.id','EP.name','EP.description','EP.method',array('HM.method','method_name'),'EP.url',array(\DB::expr("CONCAT(EP.name,' [',EP.url,']')"),"value"))->from(array('entry_points','EP'));
        $query->join(array("api_entryPoints","AEP"),"INNER")->on("EP.id","=","AEP.entryPoint_id");
        $query->join(array('http_methods','HM'),'INNER')->on('EP.method','=','HM.id');
        $query->where('AEP.api_id',$id);
        if ($httpMethod!=""){
            $query->and_where('EP.method',$httpMethod);
        }
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute($this->db_conn)->as_array();
    }
    public function getLogins($id)
    {
        //array(\DB::expr("CONCAT(L.name,' [',L.url,']')"),"value")
        $query = \DB::select('L.id','L.name','L.login_entryPoint_id','L.logout_entryPoint_id',array("L.name","value"))->from(array('logins','L'));
        $query->join(array("api_logins","AL"),"INNER")->on("AL.login_id","=","L.id");
        $query->where('AL.api_id',$id);
        if (\Input::param("limit")) {
            $query->limit(\Input::param("limit"));
        }
        if (\Input::param("offset")){
            $query->offset(\Input::param("offset"));
        }
        return $query->execute($this->db_conn)->as_array();
    }
} 