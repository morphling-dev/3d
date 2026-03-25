<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ListenerMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:make-listener {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Event Listener in Infrastructure layer';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'listener';
}
