<?php

namespace Morphling\ThreeD\Commands\Applications;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class UseCaseMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = '3d:make-usecase {name} {module}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'use_case';
}
