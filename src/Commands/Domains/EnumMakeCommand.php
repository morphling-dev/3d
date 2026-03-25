<?php

namespace Morphling\ThreeD\Commands\Domains;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class EnumMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:make-enum {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Domain Enum';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'enum';
}
