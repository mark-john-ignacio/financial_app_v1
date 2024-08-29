<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;


class SeederController extends BaseController
{
    use ResponseTrait;
    public function seedMigration(){
        $seeder = \Config\Database::seeder();
        $seeder->call("MigrationSeeder");
        if ($seeder->hasError()) {
            return $this->fail($seeder->getError());
        }
        return $this->respond(["message" => "Migration Seeder has been executed"], 200);
    }
}
