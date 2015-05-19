<?php
/**
 * Created by PhpStorm.
 * User: abeam
 * Date: 4/15/15
 * Time: 12:24 AM
 */

namespace Parameters\seed;


class Parameters extends \UNBOXAPI\Data\Seed\Seeder{

    protected static $_module = 'Parameters';
    protected static $_model = 'Parameters';

    protected static $_records = array(
        array(
            'id' => 'unbox_demo_param',
            'data_type' => 'string',
            'api_type' => null,
            'description' => 'IP Address you wish to ping',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'IP Address',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth_client_id',
            'data_type' => 'string',
            'api_type' => null,
            'description' => 'API Client ID',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Client ID',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth_secret',
            'data_type' => 'string',
            'api_type' => null,
            'description' => 'API Client Secret',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Client Secret',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth_grant',
            'data_type' => 'string',
            'api_type' => null,
            'description' => 'OAuth2.0 Grant Type being requested.',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Grant Type',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth_scope',
            'data_type' => 'string',
            'api_type' => null,
            'description' => 'OAuth2.0 Scope',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Grant Type',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth_username',
            'data_type' => 'string',
            'api_type' => null,
            'description' => '',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Username',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        ),
        array(
            'id' => 'unbox_demo_oauth_password',
            'data_type' => 'string',
            'api_type' => 'password',
            'description' => '',
            'url_param' => 0,
            'version_id' => null,
            'deprecated' => 0,
            'name' => 'Password',
            'created_by' => 'unbox_demo_user',
            'modified_by' => 'unbox_demo_user'
        )
    );
    protected static function records(){
        $records = array();
        foreach(static::$_records as $record => $values){
            $dataType = \ParameterTypes\Model\ParameterTypes::findByName($values['data_type'],1);
            if ($dataType!==null){
                $dataType = $dataType->id;
            }
            $values['data_type'] = $dataType;
            $apiType = \ParameterTypes\Model\ParameterTypes::findByName($values['api_type'],2);
            if ($apiType!==null){
                $apiType = $apiType->id;
                $values['api_type'] = $apiType;
            }
            $records[] = $values;
        }
        static::$_records = $records;
        return parent::records();
    }
    protected static $_relationships = array(
        array(
            'id' => 'unbox_demo_param',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_ep2',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'required' => '0',
                'order' => '0'
            )
        ),
        array(
            'id' => 'unbox_demo_oauth_client_id',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_oauth1',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'required' => '1',
                'default' => 'unbox_demo_api_client',
                'order' => '2',
                'login_pane' => 'advanced'
            )
        ),
        array(
            'id' => 'unbox_demo_oauth_secret',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_oauth1',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'required' => '1',
                'default' => 'unbox_demo_api_secret',
                'order' => '3',
                'login_pane' => 'advanced'
            )
        ),
        array(
            'id' => 'unbox_demo_oauth_grant',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_oauth1',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'default' => 'password',
                'required' => '1',
                'order' => '4',
                'login_pane' => 'advanced'
            )
        ),
        array(
            'id' => 'unbox_demo_oauth_scope',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_oauth1',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'required' => '1',
                'default' => 'demo_app',
                'order' => '5',
                'login_pane' => 'advanced'
            )
        ),
        array(
            'id' => 'unbox_demo_oauth_username',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_oauth1',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'required' => '1',
                'order' => '0',
                'login_pane' => 'normal'
            )
        ),
        array(
            'id' => 'unbox_demo_oauth_password',
            'name' => 'entrypoints',
            'related_id' => 'unbox_demo_oauth1',
            'related_properties' => array(
                'created_by' => 'unbox_demo_user',
                'modified_by' => 'unbox_demo_user',
                'required' => '1',
                'order' => '1',
                'login_pane' => 'normal'
            )
        ),
    );
}