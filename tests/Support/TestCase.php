<?php

namespace Morphling\ThreeD\Tests\Support;

use Morphling\ThreeD\ThreeDServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Ensure the package is loaded so all Artisan commands exist.
     *
     * @param  mixed  $app
     * @return array<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ThreeDServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Make sure generated module files go into the test app base path.
        $app['config']->set('3d.base_path', $app->basePath('modules'));
        $app['config']->set('3d.base_namespace', 'Modules');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Keep each test isolated.
        $this->cleanModules();
    }

    public function cleanModules(): void
    {
        $files = $this->app['files'];

        $modulesPath = $this->app->basePath('modules');
        if ($files->isDirectory($modulesPath)) {
            $files->deleteDirectory($modulesPath);
        }

        // ProviderManager expects this file to exist and end with `];`.
        $bootstrapProvidersPath = $this->app->basePath('bootstrap/providers.php');
        $files->ensureDirectoryExists(dirname($bootstrapProvidersPath));
        $files->put($bootstrapProvidersPath, "<?php\n\nreturn [\n    //\n];\n");
    }
}

