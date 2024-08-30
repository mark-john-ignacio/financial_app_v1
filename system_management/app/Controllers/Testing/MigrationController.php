<?php

namespace App\Controllers\Testing;

use App\Controllers\BaseController;

class MigrationController extends BaseController
{
    protected $migrate;
    public function __construct()
    {
        $this->migrate = service('migrations');
    }
    public function runMigrations()
    {
        try {
            $this->migrate->latest();
            return "Migrations completed successfully.";
        } catch (\Exception $e) {
            return "An error occurred while running migrations: " . $e->getMessage();
        }
    }

    public function rollbackLastMigration()
    {
        try {
            $this->migrate->regress(-1);
            return "Last migration rolled back successfully.";
        } catch (\Exception $e) {
            return "An error occurred while rolling back the last migration: " . $e->getMessage();
        }
    }

    public function rollbackAllMigrations()
    {
        try {
            $this->migrate->regress(0);
            return "All migrations rolled back successfully.";
        } catch (\Exception $e) {
            return "An error occurred while rolling back all migrations: " . $e->getMessage();
        }
    }
    
}