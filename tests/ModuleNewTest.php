<?php

use Morphling\ThreeD\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->cleanModules();
});

it('module:new scaffolds all 4 layers and generates expected view content', function () {
    $module = 'Transaction';

    $this->artisan('module:new', [
        'name' => $module,
    ])->assertExitCode(0);

    $base = $this->app->basePath("modules/{$module}");

    // Domain layer
    expect("{$base}/Domain/Entities/{$module}Entity.php")->toBeFile();
    expect("{$base}/Domain/ValueObjects/{$module}Status.php")->toBeFile();

    // Application layer
    expect("{$base}/Application/DTOs/{$module}Dto.php")->toBeFile();
    expect("{$base}/Application/UseCases/Get{$module}ListUseCase.php")->toBeFile();

    // Infrastructure layer
    expect("{$base}/Infrastructure/Models/{$module}Model.php")->toBeFile();
    expect("{$base}/Infrastructure/Repositories/Eloquent{$module}Repository.php")->toBeFile();

    // Delivery layer
    expect("{$base}/Delivery/Controllers/{$module}Controller.php")->toBeFile();

    $viewPath = "{$base}/Delivery/Views/index.blade.php";
    expect($viewPath)->toBeFile();

    $content = file_get_contents($viewPath);
    expect($content)->toContain('cursor://file/');
});

