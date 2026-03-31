<?php

/**
 * Helps static analysis understand Pest's `$this` binding.
 *
 * @var \Morphling\ThreeD\Tests\Support\TestCase $this
 */
 
beforeEach(function () {
    $this->cleanModules();
});

it('module component generators create valid files with correct namespaces', function (
    string $command,
    array $args,
    string $expectedFile,
    string $expectedNamespace,
    array $expectedSubstrings
) {
    $this->artisan($command, $args)->assertExitCode(0);

    $expectedFile = $this->app->basePath($expectedFile);
    expect($expectedFile)->toBeFile();

    $content = file_get_contents($expectedFile);

    // Namespace should be anchored to the module root.
    expect($content)->toContain("namespace {$expectedNamespace};");

    // Generated code should not leave stub placeholders behind.
    expect($content)->not->toContain('{{');

    foreach ($expectedSubstrings as $substring) {
        expect($content)->toContain($substring);
    }
})->with([
    'dto' => [
        'command' => 'module:make-dto',
        'args' => ['name' => 'TransactionDto', 'module' => 'Transaction'],
        'expectedFile' => 'modules/Transaction/Application/DTOs/TransactionDto.php',
        'expectedNamespace' => 'Modules\\Transaction\\Application\\DTOs',
        'expectedSubstrings' => ['readonly class TransactionDto', 'fromRequest'],
    ],
    'entity' => [
        'command' => 'module:make-entity',
        'args' => ['name' => 'TransactionEntity', 'module' => 'Transaction'],
        'expectedFile' => 'modules/Transaction/Domain/Entities/TransactionEntity.php',
        'expectedNamespace' => 'Modules\\Transaction\\Domain\\Entities',
        'expectedSubstrings' => ['class TransactionEntity', 'function getId'],
    ],
    'repo' => [
        'command' => 'module:make-repo',
        'args' => ['name' => 'EloquentTransactionRepository', 'module' => 'Transaction'],
        'expectedFile' => 'modules/Transaction/Infrastructure/Repositories/EloquentTransactionRepository.php',
        'expectedNamespace' => 'Modules\\Transaction\\Infrastructure\\Repositories',
        'expectedSubstrings' => [
            'class EloquentTransactionRepository',
            'implements TransactionRepositoryInterface',
            'use Modules\\Transaction\\Domain\\Repositories\\TransactionRepositoryInterface;',
        ],
    ],
    'controller' => [
        'command' => 'module:make-controller',
        'args' => ['name' => 'TransactionController', 'module' => 'Transaction'],
        'expectedFile' => 'modules/Transaction/Delivery/Controllers/TransactionController.php',
        'expectedNamespace' => 'Modules\\Transaction\\Delivery\\Controllers',
        'expectedSubstrings' => [
            'class TransactionController extends Controller',
            "return view('transaction::index')",
        ],
    ],
]);

