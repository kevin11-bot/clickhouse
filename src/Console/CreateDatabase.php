<?php

namespace Pioneers\ClickHouse\Console;

use Illuminate\Console\Command;
use Pioneers\ClickHouse\Connection;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ch-database {database_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbName = $this->argument('database_name');

        $client = (new Connection(false))->client;

        if (! $client->ping()) {
            exit('Error: Could not connect to ClickHouse server.');
        }

        try {
            $client->write("CREATE DATABASE IF NOT EXISTS $dbName");

            echo "Database $dbName created successfully or already exists.\n";
        } catch (\Exception $e) {
            echo 'Error creating database: '.$e->getMessage()."\n";
        }
    }
}
