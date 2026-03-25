<?php

use Morphling\ThreeD\Support\ProviderManager;
use Morphling\ThreeD\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->cleanModules();
});

it('ProviderManager can sync and register newly created module providers', function () {
    $module = 'Transaction';

    $this->artisan('module:new', [
        'name' => $module,
    ])->assertExitCode(0);

    // Reset providers.php to only run discovery (sync) behavior.
    $providersPath = $this->app->basePath('bootstrap/providers.php');
    $this->app['files']->put($providersPath, "<?php\n\nreturn [\n    //\n];\n");

    (new ProviderManager())->sync();

    $content = $this->app['files']->get($providersPath);
    expect($content)->toContain("Modules\\{$module}\\Infrastructure\\Providers\\{$module}ServiceProvider::class");
});

