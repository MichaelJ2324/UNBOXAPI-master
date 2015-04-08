<?php
/**
 * Created by PhpStorm.
 * User: mrussell
 * Date: 3/15/15
 * Time: 11:19 AM
 */

namespace UNBOXAPI\Data\DB;


class RelateTable extends Table{

    private $base_fields = array(
        'id' => array(
            'type' => 'varchar',
            'constraint' => 50,
            'null' => false,
        ),
        'date_created' => array(
            'type' => 'datetime',
            'null' => false,
        ),

        'date_modified' => array(
            'type' => 'datetime',
            'null' => false,
        ),
    );
    private $user_fields = array(
        'created_by' => array(
            'type' => 'varchar',
            'constraint' => 50,
            'null' => false,
        ),
        'modified_by' => array(
            'type' => 'varchar',
            'constraint' => 50,
            'null' => false,
        ),
    );
    private $softDelete_fields = array(
        'deleted' => array(
            'type' => 'tinyint',
            'constraint' => 1,
            'default' => 0,
            'null' => false,
        ),
        'deleted_at' => array(
            'type' => 'datetime',
            'null' => false,
        )
    );
    public function setFields(array $custom_fields,$softDelete=true,$user_fields=true){
        $fields = $this->base_fields;
        if ($user_fields) {
            $fields = array_merge($fields, $this->user_fields);
        }
        $fields = array_merge($fields,$custom_fields);
        if ($softDelete){
            $fields = array_merge($fields,$this->softDelete_fields);
        }
        parent::setFields($fields);
    }
}