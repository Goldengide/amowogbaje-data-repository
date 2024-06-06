# Database Repository Package


A Laravel package for handling database operations including data backup, data restoration, and seeding. This package allows you to back up all tables in the database and seed data from JSON files.


## Installation
You can install the package via Composer:


```bash
$ composer require amowogbaje/database-repository
```



## Usage

```bash
php artisan data:backup
```

## Migrating with Seeding Data

Your `DatabaseSeeder.php` should look like this;
```
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Amowogbaje\DatabaseRepository\DataRepository;

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

```

Anytime you migrate remember to put --seed at the end, it will look like you never dropped the table

```bash
php artisan migrate:fresh --seed 
```
> **Warning**:  Be sure your migrations are well written to avoid non-nullable errors for empty fields


