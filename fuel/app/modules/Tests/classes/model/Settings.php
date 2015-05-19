<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 11/2/14
 * Time: 9:03 AM
 */

namespace Tests\Model;


class Settings extends \Model\Module{

    protected static $_table_name = 'test_settings';
    protected static $_fields = array(
        'test_id' => array(
            'data_type' => 'varchar',
            'label' => 'Test',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 50
            ),
            'form' => array(
                'type' => 'relate',
                'module' => 'Entrypoints'
            ),
        ),
        'parameter' => array(
            'data_type' => 'varchar',
            'label' => 'Parameter',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 100
            ),
        ),
        'value' => array(
            'data_type' => 'varchar',
            'label' => 'Value',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 2048
            ),
        ),
    );
    protected static $_relationships = array(
        'has_one' => array(
            'test' => array(
                'key_from' => 'test_id',
                'model_to' => 'Tests\\Model\\Tests',
                'key_to' => 'id',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        ),
    );
} 