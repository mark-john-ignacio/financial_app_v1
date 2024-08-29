<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MigrationSeeder extends Seeder
{
    public function run()
    {
        $file = file_get_contents(APPPATH . 'Database/Seeds/MigrationSeeder.sql');

        $statements = array_filter(array_map('trim', explode(';', $file)));

        foreach ($statements as $statement) {
            $this->db->query($statement);
        }
    }
}
