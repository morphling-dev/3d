<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ObserverMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-observer {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Observer in Infrastructure layer';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'observer';
}
