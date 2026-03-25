<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Module Configuration
    |--------------------------------------------------------------------------
    */
    'base_path' => base_path('modules'),
    'base_namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Layers
    |--------------------------------------------------------------------------
    | Mendefinisikan 4 pilar utama dalam modul DDD Anda.
    */
    'layers' => [
        'domain'         => 'Domain',
        'application'    => 'Application',
        'infrastructure' => 'Infrastructure',
        'Delivery'             => 'Delivery',
    ],

    /*
    |--------------------------------------------------------------------------
    | Object Namespaces & Paths
    |--------------------------------------------------------------------------
    | Pemetaan folder yang jauh lebih lengkap sesuai kebutuhan skala menengah 
    | yang Anda minta (termasuk Async & Communication layer).
    */
    'namespaces' => [
        // Delivery Layer
        'controller'    => 'Delivery/Controllers',
        'request'       => 'Delivery/Requests',
        'resource'      => 'Delivery/Resources',
        'route'         => 'Delivery/Routes',
        'view'          => 'Delivery/Views',

        // Domain Layer
        'entity'        => 'Domain/Entities',
        'value_object'  => 'Domain/ValueObjects',
        'service'       => 'Domain/Services',
        'interface'     => 'Domain/Interfaces',
        'enum'          => 'Domain/Enums',

        // Application Layer
        'use_case'      => 'Application/UseCases',
        'dto'           => 'Application/DTOs',

        // Infrastructure Layer
        'model'         => 'Infrastructure/Models',
        'repository'    => 'Infrastructure/Repositories',
        'mapper'        => 'Infrastructure/Mappers',
        'event'         => 'Infrastructure/Events',
        'listener'      => 'Infrastructure/Listeners',
        'job'           => 'Infrastructure/Jobs',
        'notification'  => 'Infrastructure/Notifications',
        'command'       => 'Infrastructure/Commands',
        'external'      => 'Infrastructure/External',
        'observer'      => 'Infrastructure/Observers',
        'provider'      => 'Infrastructure/Providers',
        'migration'     => 'Infrastructure/Database/Migrations'
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloading (Auto-Discovery)
    |--------------------------------------------------------------------------
    */
    'autoload' => [
        'routes'     => true,
        'providers'  => true,
        'migrations' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared Module Defaults
    |--------------------------------------------------------------------------
    | Komponen yang otomatis dibuat di folder Modules/Shared saat install.
    */
    'shared' => [
        'enabled' => true,
        'core_classes' => [
            'BaseModel'    => 'Infrastructure\Models',
            'BaseUseCase'  => 'Application\UseCases',
            'ApiResponse'  => 'Infrastructure\Helpers',
        ],
    ],
];
