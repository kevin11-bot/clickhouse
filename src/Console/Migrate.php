<?php

namespace Pioneers\ClickHouse\Console;

use FilesystemIterator;
use Illuminate\Console\Command;
use UnexpectedValueException;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ch-migrate';

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
        $migrationDirectory = $this->getMigrationDirectory();

        try {
            $iterator = new FilesystemIterator($migrationDirectory, FilesystemIterator::SKIP_DOTS);

            $php_files = [];

            foreach ($iterator as $item) {
                if ($item->isFile() && $item->getExtension() === 'php') {
                    $php_files[] = $item->getPathname();
                }
            }

            if (empty($php_files)) {
                $this->info('Nothing to migrate.');
                return;
            }

            foreach ($php_files as $file) {
                $migration = require $file;

                $migration->up();

                $fileName = explode('.', basename($file))[0];

                $this->info("Migration {$fileName} executed successfully");
            }
        }catch (UnexpectedValueException) {
            $this->error("The directory in this path:$migrationDirectory does not exist");
        }
    }

    private function getMigrationDirectory(): string
    {
        return config('clickhouse.migrations.path');
    }
}
