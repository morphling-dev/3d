<?php

namespace Morphling\ThreeD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleRouteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '3d:route {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all routes for a specific Morphling 3D module';

    /**
     * Execute the console command.
     *
     * Lists all routes that belong to a specific Morphling 3D module by
     * filtering the application's routes based on the module's controller namespace.
     *
     * @return int Command exit status
     */
    public function handle(): int
    {
        $module = Str::studly($this->argument('module'));
        $namespacePattern = "Modules\\{$module}\\";

        $this->info("Filtering routes for module: {$module}...");

        // Retrieve all routes and filter by action/controller containing the module namespace
        $routes = collect(app('router')->getRoutes())->filter(function ($route) use ($namespacePattern) {
            $action = $route->getActionName();

            return is_string($action) && strpos($action, $namespacePattern) === 0;
        })->values();

        if ($routes->isEmpty()) {
            $this->warn("No routes found for module: {$module}");
            return 0;
        }

        // Mimic new Laravel route:list formatting for route output
        $maxMethod = $routes->max(fn($route) => strlen(implode('|', $route->methods())));
        $maxUri = $routes->max(fn($route) => strlen($route->uri()));
        $maxName = $routes->max(fn($route) => strlen($route->getName() ?? ''));
        $maxAction = $routes->max(fn($route) => strlen($route->getActionName()));
        $maxMiddleware = $routes->max(fn($route) => strlen(implode(', ', $route->gatherMiddleware())));

        $header = sprintf(
            "%-{$maxMethod}s  %-{$maxUri}s  %-{$maxName}s  %-{$maxAction}s  %s",
            'Method',
            'URI',
            'Name',
            'Action',
            'Middleware'
        );
        $this->line($header);
        $this->line(str_repeat('-', strlen($header)));

        foreach ($routes as $route) {
            $line = sprintf(
                "%-{$maxMethod}s  %-{$maxUri}s  %-{$maxName}s  %-{$maxAction}s  %s",
                implode('|', $route->methods()),
                $route->uri(),
                $route->getName() ?? '',
                $route->getActionName(),
                implode(', ', $route->gatherMiddleware())
            );
            $this->line($line);
        }

        return 0;
    }
}
