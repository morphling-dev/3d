<?php

namespace Modules\Shared\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Kernel;
use Modules\Shared\Delivery\Middleware\ForceJsonResponseMiddleware;

class SharedServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->prependMiddlewareToGroup('api', ForceJsonResponseMiddleware::class);

        if ($this->app->runningInConsole()) {
            $stubsDir = base_path('vendor/morphing-coding/3d/stubs');
            $targetDir = base_path('stubs/morphling-3d');

            $files = [];
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($stubsDir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                $relativePath = str_replace($stubsDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                if (strpos($relativePath, 'Shared' . DIRECTORY_SEPARATOR) !== 0 && strpos($relativePath, 'Shared/') !== 0) {
                    $files[$file->getPathname()] = $targetDir . DIRECTORY_SEPARATOR . $relativePath;
                }
            }

            if (!empty($files)) {
                $this->publishes($files, 'morphling-stubs');
            }
        }
    }
}
