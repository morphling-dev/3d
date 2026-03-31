<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Morphling\ThreeD\Support\ProviderManager;

class ModuleDiscover extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = '3d:discover';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Discover and register all module service providers';

    /**
     * Handle the execution of module discovery and registration.
     *
     * @return int  Returns 0 when the command completes successfully.
     */
    public function handle(): int
    {
        // Notify user that module scanning is starting
        $this->info('Scanning modules...');

        // Discover and synchronize module service providers
        $providerManager = new ProviderManager();
        $providerManager->sync();

        // Inform user that synchronization is complete
        $this->info('All modules have been synchronized with bootstrap/providers.php');

        return 0;
    }
}
