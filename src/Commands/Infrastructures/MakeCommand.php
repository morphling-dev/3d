<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class MakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = '3d:make-command {name} {module}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a new Console Command inside a module';

    /**
     * The type of the generator.
     *
     * @var string
     */
    protected $type = 'command';
}
