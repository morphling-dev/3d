<?php

namespace Morphling\ThreeD\Commands\Domains;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ValueObjectMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:make-vo {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Value Object';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'value_object';
}
