<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class FactoryMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-factory {name} {module} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Factory in the Infrastructure layer';

    /**
     * The file type being generated.
     *
     * @var string
     */
    protected $type = 'factory';

    /**
     * Build the class content for the generated factory.
     *
     * This method processes the stub content retrieved from the parent and replaces
     * additional placeholders specific to the factory, particularly the model name
     * and its namespace. If the --model option is not provided, it defaults to using
     * the factory name minus the 'Factory' suffix as the model name.
     *
     * @param string $name The fully qualified class name to generate.
     * @return string The processed class content ready for file writing.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        $modelArg = $this->option('model');

        // If user does not provide --model, assume model name = factory name without 'Factory'
        if (!$modelArg) {
            $modelArg = str()->replaceLast('Factory', '', $this->argument('name'));
        }

        $module = $this->argument('module');
        $details = $this->getModuleInfo($module);

        // Assume model is always located in Infrastructure\Models
        $modelNamespace = $details['root_namespace'] . '\\Infrastructure\\Models\\' . $modelArg;
        $className = $modelArg . 'Factory';

        return str_replace(
            ['{{ class }}', '{{ model }}', '{{ modelNamespace }}'],
            [$className, $modelArg, $modelNamespace],
            $stub
        );
    }
}
