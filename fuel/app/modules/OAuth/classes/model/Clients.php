<?php

namespace Oauth\Model;

class Clients extends \Model\Oauth {

    protected static $_table_name = 'oauth_clients';
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
    );
    protected static $_relationships = array(
        'belongs_to' => array(
            'session' => array(
                'key_from' => 'id',
                'model_to' => 'Oauth\\Model\\Sessions',
                'key_to' => 'client_id',
                'cascade_save' => false,
                'cascade_delete' => true,
            ),
            'redirect_uri' => array(
                'key_from' => 'id',
                'model_to' => 'Oauth\\Model\\RedirectUris',
                'key_to' => 'client_id',
                'cascade_save' => false,
                'cascade_delete' => true,
            )
        )
    );
}

?>
