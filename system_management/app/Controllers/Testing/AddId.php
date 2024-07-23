<?php

namespace App\Controllers\Testing;

use App\Controllers\BaseController;
use CodeIgniter\Files\File;

class AddId extends BaseController
{
    protected $helpers = ['form'];

    public function AddIdToSOTable()
    {
        $this->db->query('ALTER TABLE so DROP PRIMARY KEY');

        // Add id field to banks table and set it as the primary key
        $this->db->query('ALTER TABLE so ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

        echo 'id field added to so table';
    }

    public function RemoveIdFromSOTable()
    {
        $this->db->query('ALTER TABLE so DROP COLUMN id');

        // Restore primary key of banks table
        $this->db->query('ALTER TABLE so ADD PRIMARY KEY(compcode, ctranno)');

        echo 'id field removed from so table';
    }

    public function AddIdToSOTTableMigration()
    {
        $this->db->query('ALTER TABLE so_t DROP PRIMARY KEY');

        // Add id field to banks table and set it as the primary key
        $this->db->query('ALTER TABLE so_t ADD id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

        echo 'id field added to so_t table';
    }

    public function RemoveIdFromSOTTableMigration()
    {
        $this->db->query('ALTER TABLE so_t DROP COLUMN id');

        // Restore primary key of banks table
        $this->db->query('ALTER TABLE so_t ADD PRIMARY KEY(compcode, cidentity)');

        echo 'id field removed from so_t table';
    }
    
}