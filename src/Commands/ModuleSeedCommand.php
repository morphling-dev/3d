<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:seed {module} {--class=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run seeders for a specific Morphling 3D module';

    /**
     * Execute the console command.
     *
     * Tries to locate and run the seeder class for the given module.
     *
     * @return int Command exit status
     */
    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $class = $this->option('class') ?: "{$module}Seeder";
        $fullClass = "Modules\\{$module}\\Infrastructure\\Database\\Seeders\\{$class}";

        if (!class_exists($fullClass)) {
            $this->error("Seeder class not found: {$fullClass}");
            return 1;
        }

        $this->info("Seeding for module: {$module}...");

        $this->call('db:seed', [
            '--class' => $fullClass
        ]);

        $this->info("Seeding completed for module: {$module}.");
        return 0;
    }
}
