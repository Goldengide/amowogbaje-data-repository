<?php

namespace Amowogbaje\DatabaseRepository;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $seed = new DataRepository;
        $tableArray = $seed->getAllTablesInADB();
        foreach ($tableArray as $table) {
            $seed->setTableName($table);
            $seed->dataTableSeederFunction();
        }
    }
}
