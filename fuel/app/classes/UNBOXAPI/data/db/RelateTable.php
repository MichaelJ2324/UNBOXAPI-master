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
            'type' => 'int',
            'constraint' => 11,
            'auto_inc' => true,
            'unsigned' => true,
            'null' => false,
        )
    );
    public function setFields(array $custom_fields){
        $fields = $this->base_fields;
        $fields = array_merge($fields,$custom_fields);
        parent::setFields($fields);
    }
}