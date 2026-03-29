# Morphling 3D Documentation

Morphling 3D is a high-velocity scaffolding engine for Laravel that enforces a **Domain-Driven Design (DDD)** modular structure. It solves the "Big Ball of Mud" problem in large-scale applications by providing a rigid yet flexible framework for isolating business logic from infrastructure.

---

## Executive Summary
Modern enterprise applications often outgrow the standard Laravel `app/` directory. Morphling 3D automates the transition to a modular architecture, eliminating the manual overhead of creating DTOs, Mappers, and Service Providers. It ensures that your codebase remains refactor-friendly and architecturally sound from the first `artisan` command.

---

## Key Concepts

### The Mental Model: "The Layered Fortress"
Think of your application as a fortress. In a standard MVC "village," everything is mixed together. In Morphling 3D, we use four distinct layers:

1.  **The Gates (Delivery):** How the world interacts with you (Routes, Controllers).
2.  **The Guard (Application):** Orchestrates the movement of data (Use Cases, DTOs).
3.  **The Throne Room (Domain):** The heart of the kingdom where the rules are made (Entities, Enums).
4.  **The Foundation (Infrastructure):** The tools and stone used to build (Eloquent, Repositories).

[Placeholder: Diagram showing the unidirectional flow from Delivery -> Application -> Domain <- Infrastructure]

---

## Quick Start (The 2-Minute Rule)

Get a fully-functional, DDD-compliant module running in seconds.

```bash
# 1. Install via Composer
composer require morphling/morphling-3d

# 2. Initialize a new module
php artisan morphling:make:module Order

# 3. Profit
# Your module is now live at /modules/Order with auto-registered routes.
```

---

## Technical Reference

### Installation
Ensure your `composer.json` is prepared for modular autoloading before running the installer.

```bash
composer require morphling/morphling-3d --dev
php artisan morphling:install
```

### Module Structure
Every module follows a strict four-layer hierarchy:

| Layer | Responsibility | Contents |
| :--- | :--- | :--- |
| **Delivery** | HTTP/UI Glue | Controllers, Routes, Blade Views, Resources |
| **Application** | Use Case Logic | DTOs, Service Orchestration |
| **Domain** | Business Rules | Entities, Value Objects, Interfaces, Enums |
| **Infrastructure** | Persistence | Eloquent Models, Repositories, Migrations |

### Configuration
Edit `config/morphling.php` to customize the scaffolding behavior.

| Parameter | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `path` | `string` | `'modules'` | The root directory for your modules. |
| `auto_discovery` | `bool` | `true` | Enables automatic registration of providers/routes. |
| `editor_link` | `string` | `'vscode'` | Deep-link protocol (`vscode`, `phpstorm`, `cursor`). |

   
---

## Advanced Patterns

### Enforced Data Integrity with DTOs
Morphling 3D discourages passing raw Request objects into your business logic. Instead, use the generated Data Transfer Objects (DTOs):

```php
// Delivery/Controllers/OrderController.php
public function store(StoreOrderRequest $request, CreateOrderUseCase $useCase)
{
    // Morphling generates the 'fromRequest' mapping automatically
    $dto = OrderDTO::fromRequest($request); 
    
    return $useCase->execute($dto);
}
```

---

## Best Practices

### The Do's
* **Do** keep your **Domain** pure. It should not know about the database or the web.
* **Do** use the `morphling:make` commands to ensure all mapping primitives are created.
* **Do** leverage **Auto-Discovery** to keep your `app/Providers` clean.

### The Don'ts
* **Don't** reference a Controller from the Domain layer.
* **Don't** bypass the Application layer for complex logic; keep Controllers thin.
* **Don't** manually register Service Providers for modules (Morphling handles this).

---

## Troubleshooting

### 1. Module Routes Not Found
**Issue:** You created a module but the routes return a 404.
**Solution:** Ensure your module directory matches the casing in your command. Run `php artisan route:clear` and check if `auto_discovery` is enabled in `config/morphling.php`.

### 2. Class Not Found (Autoloading)
**Issue:** PHP cannot find classes inside the `modules/` directory.
**Solution:** Check your `composer.json`. You may need to add:
```json
"psr-4": {
    "Modules\\": "modules/"
}
```
Then run `composer dump-autoload`.

### 3. Deep-Linking Not Working
**Issue:** Clicking the "Edit in Editor" link in a view does nothing.
**Solution:** Ensure your `config/morphling.php` matches your preferred IDE. Some browsers require a one-time permission to open external protocols (e.g., `vscode://`).
