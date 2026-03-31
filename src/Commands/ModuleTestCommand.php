<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class ModuleTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:test {module?} {--unit} {--feature}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tests for a specific Morphling 3D module';

    /**
     * Execute the console command.
     *
     * @return int  Command exit code.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $basePath = config('3d.base_path', base_path('modules'));

        if ($module) {
            $moduleName = Str::studly($module);
            $testPath = "{$basePath}/{$moduleName}/Tests";

            if ($this->option('unit')) {
                $testPath .= '/Unit';
            }
            if ($this->option('feature')) {
                $testPath .= '/Feature';
            }

            $this->runModuleTest($testPath, $moduleName);
        } else {
            $this->info('Running tests for all modules...');
            $this->runModuleTest("{$basePath}/*/Tests");
        }

        return 0;
    }

    /**
     * Run the module tests using Pest or PHPUnit.
     *
     * @param  string  $path  The path to the tests directory or file.
     * @param  string  $name  The name of the module being tested.
     * @return void
     */
    protected function runModuleTest(string $path, string $name = 'All'): void
    {
        $this->info("Testing Module: {$name}...");

        // Determine whether Pest or PHPUnit is available.
        $binary = file_exists(base_path('vendor/bin/pest')) ? 'vendor/bin/pest' : 'vendor/bin/phpunit';

        // Run the process using Symfony Process for real-time output.
        $process = new Process([PHP_BINARY, $binary, $path]);
        if (Process::isTtySupported()) {
            $process->setTty(true);
        }
        $process->setTimeout(null);

        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });
    }
}
