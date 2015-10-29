<?php

namespace ParameterTypes\Model;

class ApiTypes extends ParameterTypes {

    protected static $_relationships = array(
        'has_many' => array(
            'parameters' => array(
                'key_from' => 'id',
                'model_to' => 'Parameters\\Model\\Parameters',
                'key_to' => 'api_type',
                'cascade_save' => true,
                'cascade_delete' => false,
            ),
        )
    );

    protected static $_conditions = array(
        'order_by' => array('name' => 'asc'),
        'where' => array(
            array('deleted', '=', 0),
            array('type','=',2)
        ),
    );

}