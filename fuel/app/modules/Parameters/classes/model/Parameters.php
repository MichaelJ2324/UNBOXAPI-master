<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 7/4/14
 * Time: 10:04 PM
 */

namespace Parameters\Model;

class Parameters extends \Model\Unbox{

    protected static $_table_name = 'parameters';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'int',
            'label' => 'Parameter ID',
            'null' => false,
            'auto_inc' => true
        ),
        'data_type' => array(
            'data_type' => 'varchar',
            'label' => 'Data Type',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(1),
                'max_length' => array(50)
            ),
            'form' => array(
                'type' => 'select',
                'class' => 'select2',
                'options' => array(),
                'collection' => 'dataTypes'
            )
        ),
        'api_type' => array(
            'data_type' => 'varchar',
            'label' => 'API Type',
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(1),
                'max_length' => array(50)
            ),
            'form' => array(
                'type' => 'select',
                'class' => 'select2',
                'options' => array(),
                'collection' => 'apiTypes'
            )
        ),
        'name' => array(
            'data_type' => 'varchar',
            'label' => 'Name',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'min_length' => array(1),
                'max_length' => array(70)
            ),
            'form' => array('type' => 'text'),
        ),
        'description' => array(
            'data_type' => 'varchar',
            'label' => 'Description',
            'auto_inc' => false,
            'validation' => array(
                'max_length' => array(500)
            ),
            'form' => array(
                'type' => 'textarea'
            ),
        ),
        'version' => array(
            'data_type' => 'smallint',
            'label' => 'Version',
            'default' => 0,
            'auto_inc' => false,
            'validation' => array(
                'required' => true
            ),
            'form' => false,
        ),
        'url_param' => array(
            'data_type' => 'tinyint',
            'label' => 'URL Parameter?',
            'default' => 0,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
            ),
            'form' => array(
                'type' => 'checkbox'
            ),
        ),
        'deleted' => array(
            'data_type' => 'tinyint',
            'label' => 'Deleted',
            'default' => 0,
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => array(500)
            ),
            'form' => false,
        ),
        'deprecated' => array(
            'data_type' => 'tinyint',
            'label' => 'Deprecated',
            'default' => 0,
            'auto_inc' => false,
            'validation' => array(
                'required' => true,
                'max_length' => array(500)
            ),
            'form' => array(
                'type' => 'checkbox',
                'disabled' => 'disabled'
            ),
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
    protected static $_has_one = array(
        'data_type' => array(
            'key_from' => 'data_type',
            'model_to' => 'ParameterTypes\\Model\\ParameterTypes',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'api_type' => array(
            'key_from' => 'api_type',
            'model_to' => 'ParameterTypes\\Model\\ParameterTypes',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'past_version' => array(
            'key_from' => 'id',
            'model_to' => 'Parameters\\Model\\Versions',
            'key_to' => 'past_parameter_id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'new_version' => array(
            'key_from' => 'id',
            'model_to' => 'Parameters\\Model\\Versions',
            'key_to' => 'new_parameter_id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );
    protected static $_many_many = array(
        'entryPoints' => array(
            'key_from' => 'id',
            'key_through_from' => 'parameter_id',
            'table_through' => 'entryPoint_parameters',
            'key_through_to' => 'entryPoint_id',
            'model_to' => 'EntryPoints\\Model\\EntryPoints',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );

    public function getEntryPointParam($param,$entryPoint,$url=""){
        $query = \DB::select('P.id','P.data_type','P.api_type','P.name','P.description','P.url_param',array('EPP.id','related_entryPoint'),'EPP.required','EPP.order','EPP.login_pane')->from(array('parameters','P'));
        $query->join(array('entryPoint_parameters','EPP'),'INNER')->on('P.id','=','EPP.parameter_id');
        $query->where('P.id',$param);
        $query->and_where('EPP.entryPoint_id',$entryPoint);
        if ($url!=""){
            if ($url==false){
                $query->and_where('P.url_param',"0");
            }else{
                $query->and_where('P.url_param',"1");
            }
        }
        return $query->execute(self::$_connection)->as_array();
    }
} 