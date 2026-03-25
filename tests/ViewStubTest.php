<?php

use Morphling\ThreeD\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->cleanModules();
});

it('generated index.blade.php contains cursor://file/ openCodeEditor script', function () {
    $module = 'Transaction';

    $this->artisan('module:make-view', [
        'name' => 'index',
        'module' => $module,
    ])->assertExitCode(0);

    $viewPath = $this->app->basePath("modules/{$module}/Delivery/Views/index.blade.php");
    expect($viewPath)->toBeFile();

    $content = file_get_contents($viewPath);
    expect($content)->toContain('cursor://file/');
});

