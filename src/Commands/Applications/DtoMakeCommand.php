<?php

namespace Morphling\ThreeD\Commands\Applications;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class DtoMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-dto {name} {module}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'dto';
}
