<?php

namespace App\Controllers\Testing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;


class SeederController extends BaseController
{
    use ResponseTrait;
    public function seedMigration(){
        $seeder = \Config\Database::seeder();
        $seeder->call("MigrationSeeder");
        return $this->respond(["message" => "Migration Seeder has been executed"], 200);
    }
}
