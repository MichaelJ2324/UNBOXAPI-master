<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 11:51 AM
 */

namespace EntryPoints\Model;


class Versions extends \Model\Unbox{

    protected static $_table_name = 'entryPoint_versions';
    protected static $_properties = array(
        'id' => array(
            'data_type' => 'int',
            'label' => 'EntryPoint Version ID',
            'null' => false,
            'auto_inc' => true
        ),
        'past_entryPoint_id' => array(
            'data_type' => 'int',
            'label' => 'Past EntryPoint ID',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => false,
        ),
        'new_entryPoint_id' => array(
            'data_type' => 'int',
            'label' => 'New EntryPoint ID',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => false,
        ),
        'change_description' => array(
            'type' => 'varchar',
            'label' => 'Description',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required',
                'max_length' => array(500)
            ),
            'form' => array('type' => 'text'),
        ),
        'change_data' => array(
            'type' => 'text',
            'label' => 'Description',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required',
                'max_length' => array(500)
            ),
            'form' => false,
        ),
        'date_created' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => array('type' => 'text'),
        ),
        'date_modified' => array(
            'data_type' => 'datetime',
            'label' => 'Date Created',
            'default' => '',
            'null' => false,
            'auto_inc' => false,
            'validation' => array(
                'required'
            ),
            'form' => array('type' => 'text'),
        ),
    );
    protected static $_belongs_to = array(
        'pastEntryPoints' => array(
            'key_from' => 'past_entryPoint_id',
            'model_to' => 'EntryPoints\\Model\\EntryPoints',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
        'newEntryPoints' => array(
            'key_from' => 'new_entryPoint_id',
            'model_to' => 'EntryPoints\\Model\\EntryPoints',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        ),
    );
} 