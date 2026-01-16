<?php

namespace Pioneers\ClickHouse\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ch-migration {migration_name}';

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
        $migrationName = $this->argument('migration_name').'.php';

        $fs = new Filesystem;

        $now = Carbon::now()->format('Y_m_d_His');

        $migrationDirectory = $this->getMigrationDirectory();
        $fileName = $now.'_'.$migrationName;

        $fs->ensureDirectoryExists($migrationDirectory);

        $filePath = "$migrationDirectory/$fileName";
        $stub = $fs->get($this->getStub());

        $fs->put($filePath, $stub);
    }

    private function getMigrationDirectory(): string
    {
        return config('clickhouse.migrations.path');
    }

    private function getStub(): string
    {
        return __DIR__.'/../../stubs/migration.stub';
    }
}
