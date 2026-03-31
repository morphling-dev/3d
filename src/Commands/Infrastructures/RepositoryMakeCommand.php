<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class RepositoryMakeCommand extends BaseGeneratorCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = '3d:make-repo {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Implementation (Eloquent)';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'repository';
}
