<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckBaseUrl extends Command
{
    protected $signature = 'config:check-url';
    protected $description = 'Check the base URL configuration';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $baseUrl = config('app.url');
        $this->info('Base URL: ' . $baseUrl);
    }
}
