# Database Repository Package


A Laravel package for handling database operations including data backup, data restoration, and seeding. This package allows you to back up all tables in the database and seed data from JSON files.


## Installation
You can install the package via Composer:


```bash
$ composer require amowogbaje/database-repository
```

## Publish the Seeder

To publish the custom `DatabaseSeeder` class to your application's `database/seeders` directory, run:
```bash
$ php artisan vendor:publish --tag=amowogbaje-seeder --force
```
Note: This will override the default DatabaseSeeder with the one provided by this package.

## Usage

```bash
php artisan data:backup
```

## Migrating with Seeding Data

Anytime you migrate remember to put --seed at the end, it will look like you never dropped the table

```bash
php artisan migrate:fresh --seed 
```
> **Warning**:  Be sure your migrations are well written to avoid non-nullable errors for empty fields


