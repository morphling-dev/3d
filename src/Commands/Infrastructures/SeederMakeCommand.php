<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class SeederMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-seeder {name} {module} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Seeder in the Infrastructure layer';

    /**
     * The file type being generated.
     *
     * @var string
     */
    protected $type = 'seeder';

    /**
     * Build the class content for the generated seeder.
     *
     * This method processes the stub content retrieved from the parent and replaces
     * additional placeholders specific to the seeder, particularly the model name
     * and its namespace. If the --model option is not provided, it defaults to using
     * the seeder name minus the 'Seeder' suffix as the model name.
     *
     * @param string $name The fully qualified class name to generate.
     * @return string The processed class content ready for file writing.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        $modelArg = $this->option('model');

        // If user does not provide --model, assume model name = seeder name without 'Seeder'
        if (!$modelArg) {
            $modelArg = str()->replaceLast('Seeder', '', $this->argument('name'));
        }

        $module = $this->argument('module');
        $details = $this->getModuleInfo($module);

        // Assume model is always located in Infrastructure\Models
        $modelNamespace = $details['root_namespace'] . '\\Infrastructure\\Models\\' . $modelArg;
        $className = $this->argument('name');

        return str_replace(
            ['{{ class }}', '{{ model }}', '{{ modelNamespace }}'],
            [$className, $modelArg, $modelNamespace],
            $stub
        );
    }
}
