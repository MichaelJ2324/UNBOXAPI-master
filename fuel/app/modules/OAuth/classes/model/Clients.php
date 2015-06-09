<?php

namespace Oauth\Model;

class Clients extends \Model\Auth {

    protected static $_table_name = 'clients';
    protected static $_fields = array(
        'client_id' => array(
            'data_type' => 'varchar',
            'label' => 'Client ID',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'secret' => array(
            'data_type' => 'varchar',
            'label' => 'Secret',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
        'name' => array(
            'data_type' => 'varchar',
            'label' => 'Name',
            'null' => false,
            'validation' => array(
                'required' => true,
                'max_length' => 255
            ),
        ),
		'type' => array(
			'data_type' => 'varchar',
			'label' => 'Type',
			'null' => false,
			'default' => 'api_user',
			'validation' => array(
				'required' => true,
				'max_length' => 25
			),
		),
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'session' => array(
                'key_from' => 'id',
                'model_to' => 'OAuth\\Model\\Sessions',
                'key_to' => 'client_id',
                'cascade_save' => false,
                'cascade_delete' => false,
            ),
            'redirect_uri' => array(
                'key_from' => 'id',
                'model_to' => 'OAuth\\Model\\RedirectUris',
                'key_to' => 'client_id',
                'cascade_save' => false,
                'cascade_delete' => true,
            ),
			'user' => array(
				'key_from' => 'id',
				'model_to' => 'OAuth\\Model\\Users',
				'key_to' => 'api_client_id',
				'cascade_save' => false,
				'cascade_delete' => false,
			)
        ),
    );
}

?>
