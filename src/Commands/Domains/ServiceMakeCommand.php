<?php

namespace Morphling\ThreeD\Commands\Domains;

use Morphling\ThreeD\Commands\BaseGeneratorCommand;

class ServiceMakeCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-service {name} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Domain Service';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'service';
}
