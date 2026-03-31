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
    }
}
