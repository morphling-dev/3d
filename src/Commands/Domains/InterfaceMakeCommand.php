<?php

namespace Morphling\ThreeD\Commands\Domains;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class InterfaceMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:make-interface {name} {module}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a new Domain Interface';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'interface';
}
