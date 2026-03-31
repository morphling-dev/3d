<?php

namespace Morphling\ThreeD\Commands\Domains;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class EntityMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-entity {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Domain Entity';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'entity';
}
