<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ModuleServiceProviderMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-provider {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service Provider for the module';

    /**
     * The file type being generated.
     *
     * @var string
     */
    protected $type = 'provider';

    protected function getNameInput(): string
    {
        return $this->argument('module') . 'ServiceProvider';
    }
}
