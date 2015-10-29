<?php

namespace UNBOXAPI\Data\DB;

class Helper {

    public static function updateTable(Table $newTable, $oldTable){


    }

    /**
     * Migrating a table, will create a new Table, and migrate all data from $oldTable to $newTable
     * If $oldTable and $newTable have the same name, $oldTable is renamed with appended _{#}
     * @param Table $newTable
     * @param $oldTable
     */
    public static function migrateTable(Table $newTable, $oldTable){

    }

}