<?php

namespace Morphling\ThreeD\Commands\Deliveries;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ResourceMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-resource {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API Resource in the UI layer';

    /**
     * The type definition for the generator command.
     *
     * @var string
     */
    protected $type = 'resource';
}
