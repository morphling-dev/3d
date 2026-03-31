<?php

namespace Morphling\ThreeD\Commands\Infrastructures;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ModelMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:make-model {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Model in the Infrastructure layer';

    /**
     * The file type being generated.
     *
     * @var string
     */
    protected $type = 'model';
}
