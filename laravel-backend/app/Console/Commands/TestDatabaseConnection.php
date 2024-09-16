<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class TestDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the database connection';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            DB::connection()->getPdo();
            $this->info('Database connection is successful!');
        } catch (Exception $e) {
            $this->error('Could not connect to the database. Please check your configuration.');
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
