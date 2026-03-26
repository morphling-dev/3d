# Morphling 3D (Domain Driven Design)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/morphling-dev/3d.svg?style=flat-square)](https://packagist.org/packages/morphling-dev/3d)
[![Total Downloads](https://img.shields.io/packagist/dt/morphling-dev/3d.svg?style=flat-square)](https://packagist.org/packages/morphling-dev/3d)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

**Morphling 3D** is a Laravel scaffolding engine designed for building enterprise-scale applications using **Domain-Driven Design (DDD)** and **Hexagonal Architecture** principles.

Stop writing repetitive boilerplate. Focus on your business logic and let Morphling 3D handle the folder structure for you.

---

## 🌟 Why Choose Morphling 3D?

Not just a folder generator, Morphling 3D brings high architectural standards into your daily workflow:

- **True Separation of Concerns:** Strictly separates Domain (Core Logic), Application (Orchestration), Infrastructure (Technical), and Delivery (Interface) layers.
- **Zero-Config Discovery:** Service Providers, Routes, and Migrations inside your modules are discovered automatically. No more manual registration in `config/app.php`.
- **Data Integrity First:** Automatically generates **DTOs** and **Mappers** to ensure data transferred between layers remains valid and controlled.
- **Great Developer Experience (DX):** Smart scripts to open newly generated files directly in your favorite editor (**Cursor, VSCode, or PHPStorm**) via links from the UI landing page.
- **Shared Kernel Ready:** Includes reusable components like `ApiResponse`, `BaseUseCase`, `BaseModel`, and `HttpStatus` constants.

---

## 📦 Installation

1. Require the package via Composer:

```bash
composer require morphling-dev/3d
```

2. Run the install command to prepare the `modules/` folder and Shared Kernel:

```bash
php artisan 3d:install
```

3. Add your modules namespace to the project's `composer.json`:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
}
```

Then run `composer dump-autoload`.

## Quick Start (Docs)

Start with: [Quick Start](docs/quick-start.md)

For a full end-to-end walkthrough: [First Module Tutorial](docs/first-module.md)

For strict boundaries: [Architectural Rules](docs/rules.md)

---

## 🏛️ Module Architecture

Every generated module follows a robust 4-layer structure:

```text
modules/
└── {ModuleName}/
    ├── Application/        # DTOs, UseCases (Application Services)
    ├── Domain/             # Entities, Value Objects, Enums, Interfaces
    ├── Infrastructure/     # Eloquent Models, Repositories, Mappers, Jobs
    └── Delivery/           # Controllers, Routes, Requests, Resources, Views
```

---

## 🛠️ Usage (Artisan Commands)

### 1. Generate a Full Module

Create a complete module along with all its folders:

```bash
php artisan module:new Order
```

### 2. Monitor Modules

See the list of active modules and their provider registration status:

```bash
php artisan module:list
```

### 3. Layer-Specific Generators

| Layer              |                                       Artisan Command                                       |
| :----------------- | :-----------------------------------------------------------------------------------------: |
| **Domain**         |      `module:make-entity`, `module:make-vo`, `module:make-enum`, `module:make-service`      |
| **Application**    |                          `module:make-usecase`, `module:make-dto`                           |
| **Infrastructure** |   `module:make-model`, `module:make-repo`, `module:make-mapper`, `module:make-migration`    |
| **Delivery**       | `module:make-controller`, `module:make-request`, `module:make-resource`, `module:make-view` |

---

## ⚙️ Configuration

You can customize namespaces and default folders in `config/3d.php`:

```php
return [
    'base_path' => base_path('modules'),
    'base_namespace' => 'Modules',

    'namespaces' => [
        'use-case'     => 'Application/UseCases',
        'entity'       => 'Domain/Entities',
        'value-object' => 'Domain/ValueObjects',
        'repository'   => 'Infrastructure/Repositories',
        // ... customize as you need
    ],
];
```

---

## 🔗 Links & Resources

- **GitHub Repository:** [https://github.com/morphling-dev/3d](https://github.com/morphling-dev/3d)
- **Documentation:** [https://docs.morphling.dev/3d](https://docs.morphling.dev/3d)
- **Issue Tracker:** [Report a Bug](https://github.com/morphling-dev/3d/issues)

---

## 🤝 Contribution

We welcome contributions! If you want to add new stubs or generator features, please open a Pull Request to the `main` branch.

## 📄 License

Licensed under the **MIT** license. Use responsibly to build great applications.

---

Created by **[Indra Ranuh](https://github.com/indraranuh-dev)** & the **[Morphling Coding](https://github.com/morphling-dev)**
