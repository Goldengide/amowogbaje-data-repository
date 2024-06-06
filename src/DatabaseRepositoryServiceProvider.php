<?php

namespace Amowogbaje\DatabaseRepository;

use Illuminate\Support\ServiceProvider;

class DatabaseRepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DataRepository::class, function ($app) {
            return new DataRepository();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Amowogbaje\DatabaseRepository\Console\Commands\BackupDataCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../database/seeders/DatabaseSeeder.php' => database_path('seeders/DatabaseSeeder.php'),
            ], 'amowogbaje-seeder');
        }
    }
}
