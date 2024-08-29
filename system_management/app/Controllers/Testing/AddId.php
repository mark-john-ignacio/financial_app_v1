<?php

namespace App\Controllers\Testing;

use App\Controllers\BaseController;

class AddId extends BaseController
{

    public function runAllMigration() {
        $migrations = service('migrations');
        try {
            // Run all available migrations
            $migrations->setNamespace(null)->latest('default');
    
            return $this->response->setStatusCode(200)->setBody('Migration ran successfully.');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to run migration.';
            return $this->response->setStatusCode(500)->setBody($errorMessage);
        }
    }

    public function rollbackAllMigration() {
        $migrations = service('migrations');
    
        try {
            // Rollback the last batch of migrations
            $migrations->setNamespace(null)->regress(0, 'default');
    
            return $this->response->setStatusCode(200)->setBody('Migration rolled back successfully.');
        } catch (\Exception $e) {
    
            // Provide a minimal error message in the response
            return $this->response->setStatusCode(500)->setBody('Failed to roll back migration.');
        }
    }
    
}