<?php

namespace App\Controllers\Testing;

use App\Controllers\BaseController;

class AddId extends BaseController
{

    public function AddIdToSOTable()
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Drop primary key
            $db->query('ALTER TABLE so DROP PRIMARY KEY');

            // Add id field to so table and set it as the primary key
            $db->query('ALTER TABLE so ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                // Transaction failed, handle error
                return $this->response->setStatusCode(500)->setBody('Failed to alter table schema.');
            }

            // Success response
            return $this->response->setStatusCode(200)->setBody('Table schema altered successfully.');
        } catch (\Throwable $e) {
            // Handle exception
            return $this->response->setStatusCode(500)->setBody('An error occurred: ' . $e->getMessage());
        }
    }

    public function RemoveIdFromSOTable()
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Drop id field
            $db->query('ALTER TABLE so DROP COLUMN id');

            // Restore primary key
            $db->query('ALTER TABLE so ADD PRIMARY KEY(compcode, ctranno)');

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                // Transaction failed, handle error
                return $this->response->setStatusCode(500)->setBody('Failed to alter table schema.');
            }

            // Success response
            return $this->response->setStatusCode(200)->setBody('Table schema altered successfully.');
        } catch (\Throwable $e) {
            // Handle exception
            return $this->response->setStatusCode(500)->setBody('An error occurred: ' . $e->getMessage());
        }
    }

    public function AddIdToSOTTableMigration()
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Drop primary key
            $db->query('ALTER TABLE so_t DROP PRIMARY KEY');

            // Add id field to so table and set it as the primary key
            $db->query('ALTER TABLE so_t ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                // Transaction failed, handle error
                return $this->response->setStatusCode(500)->setBody('Failed to alter so_t table schema.');
            }

            // Success response
            return $this->response->setStatusCode(200)->setBody('so_t Table schema altered successfully.');
        } catch (\Throwable $e) {
            // Handle exception
            return $this->response->setStatusCode(500)->setBody('An error occurred: ' . $e->getMessage());
        }

    }

    public function RemoveIdFromSOTTableMigration()
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Drop id field
            $db->query('ALTER TABLE so_t DROP COLUMN id');

            // Restore primary key
            $db->query('ALTER TABLE so_t ADD PRIMARY KEY(compcode, cidentity)');

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                // Transaction failed, handle error
                return $this->response->setStatusCode(500)->setBody('Failed to alter so_t table schema.');
            }

            // Success response
            return $this->response->setStatusCode(200)->setBody('so_t Table schema altered successfully.');
        } catch (\Throwable $e) {
            // Handle exception
            return $this->response->setStatusCode(500)->setBody('An error occurred: ' . $e->getMessage());
        }
    }
    
}