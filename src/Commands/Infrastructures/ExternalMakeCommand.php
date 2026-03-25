<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ExternalMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:make-external {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new External class in Infrastructure layer';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'external';
}
