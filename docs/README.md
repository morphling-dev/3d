# Morphling 3D Documentation

Morphling 3D is an architectural framework for Laravel that enforces a **Domain-Driven Design (DDD)** and **Hexagonal Architecture** modular structure. It addresses the "Big Ball of Mud" problem in large-scale Laravel applications by providing structure to separate business logic from infrastructure in a predictable, scalable way.

---

## Executive Summary

Modern enterprise applications often outgrow Laravel’s default `app/` directory and typical service/class sprawl. Morphling 3D automates the transition to a modular, layered architecture—removing the need for manual boilerplate, enforcing module conventions, and ensuring a refactor-friendly codebase with every `artisan` command.

---

## Key Concepts

### Mental Model: The Four-Layer System

In a traditional Laravel (MVC) structure, responsibilities quickly become mixed. Morphling 3D enforces these layers for each module:

1. **Delivery (Controller/Routes):** How the world interacts with your app.
2. **Application (UseCases/DTOs):** Orchestrates business operations and data transfer between things.
3. **Domain (Entities/Rules):** Business rules. Contains pure logic, entities, value objects, and contracts.
4. **Infrastructure (Persistence/External):** Concrete implementations—database models, repositories, APIs.

*(Each layer has a one-way dependency; the Domain is always insulated from other concerns.)*

---

## Quick Start

Get a modular, DDD structure in minutes:

```bash
# 1. Install via Composer
composer require morphling-dev/3d

# 2. Initialize Morphling for your project
php artisan 3d:install

# 3. Create a new module
php artisan 3d:new Order

# 4. (Optional) Generate module components
php artisan 3d:make-dto
php artisan 3d:make-usecase
# ...etc

# Your module is at modules/Order and ready to use!
```

---

## Technical Reference

### Installation

Make sure your `composer.json` has the appropriate autoload config before you use modules:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "modules/"
    }
}
```

Then:

```bash
composer require morphling-dev/3d
php artisan 3d:install
composer dump-autoload
```

### Module Structure

Each module is strictly organized:

| Layer           | Responsibility        | Examples                                                          |
|-----------------|----------------------|-------------------------------------------------------------------|
| Delivery        | HTTP/UI interface    | Controllers, Routes, Resources, FormRequests                      |
| Application     | Application logic    | DTOs, UseCases, Services                                          |
| Domain          | Business logic       | Entities, ValueObjects, Interfaces, Enums                         |
| Infrastructure  | Data access/Utility | Models, Repositories, Persistence, Migrations, External Gateways  |

### Configuration

Configure paths, namespaces, and behaviors in `config/3d.php`:

| Parameter      | Type     | Default        | Description                                     |
| :------------- | :------- | :-------------| :-----------------------------------------------|
| `base_path`    | string   | `'modules'`   | Root directory for all modules                   |
| `auto_discovery` | bool   | `true`        | Auto-register routes/providers for each module   |
| `base_namespace` | string | `'Modules'`   | Root PHP namespace for modules                   |

---

## Advanced Patterns

### Data Integrity with DTOs

Morphling 3D discourages passing raw Request objects to business logic. Instead, use generated DTOs to encapsulate validated data:

```php
// Delivery/Controllers/OrderController.php
public function store(StoreOrderRequest $request, CreateOrderUseCase $useCase)
{
    // DTO mapping generated automatically by Morphling
    $dto = OrderDTO::fromRequest($request); 
    return $useCase->execute($dto);
}
```

---

## Best Practices

### Do

* **Do** keep your Domain pure—no references to HTTP, Eloquent, or Controller logic.
* **Do** use Morphling `3d:make-*` commands to ensure correct conventions and mapping are followed.
* **Do** use Auto-Discovery to manage module providers/routes (no manual provider registration).

### Don’t

* **Don’t** call a Controller from anywhere except the Delivery layer.
* **Don’t** place business rules, database queries, or utilities directly in Controllers.
* **Don’t** bypass Application or Domain layers for complex logic.

---

## Troubleshooting

### 1. Module Routes Not Found

**Issue:** Routes in your newly created module return 404.  
**Solution:**  
- Make sure the casing of your module folder matches what you used in the `3d:new` command.
- Run `php artisan route:clear`.
- Check that `'auto_discovery' => true` is set in `config/3d.php`.

### 2. Class Not Found (Autoloading)

**Issue:** PHP cannot locate classes in `modules/`.  
**Solution:**  
- Ensure your `composer.json` has:

```json
"psr-4": {
    "App\\": "app/",
    "Modules\\": "modules/"
}
```
- Then run `composer dump-autoload`.

### 3. Deep-Linking Not Working

**Issue:** Clicking “Edit in Editor” in a view has no effect.  
**Solution:**  
- Make sure `config/3d.php` uses your correct IDE protocol, e.g., `'editor_link' => 'vscode'`.
- Some browsers may require permission to open external protocols (such as `vscode://`).

