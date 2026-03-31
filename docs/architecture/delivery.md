# The Delivery Layer: The Interface Boundary

The **Delivery Layer** is the entry and exit point of your module—it's where your module connects with the outside world. This includes web browsers via HTTP, APIs consumed by mobile apps, or even CLI commands. Its core job is to translate incoming requests into a form usable by the inner layers, and format results from the system into responses required by the user or client. **It must never contain business logic itself.**

---

## Executive Summary
Within Morphling 3D, the Delivery Layer is strictly dedicated to **handling Input and Output operations**. This covers: routing, executing middleware, validating incoming requests, mapping raw inputs to DTOs, and formatting outgoing responses (e.g., JSON, Blade Views, redirects). The Delivery Layer’s role is to be the boundary and translator—it must not contain any decisions or business rules.

> [!NOTE]
> **Layer Role:** `Interface` | **Can depend on:** `Application Layer (Use Cases, DTOs)`

---

## Key Concepts: The "Translator" Model
Picture the Delivery layer as a concierge desk at a grand hotel. The guest presents a need (HTTP request); the concierge checks their credentials (validation), fills out a structured internal form (DTO), and passes it on to management (the UseCase). Once management processes the request, the concierge relays the results back to the guest—polished and ready.

---

## What Morphling 3D Generates

Morphling 3D's code generation ensures you never mix responsibilities. Every common interface point has a dedicated generator:

| Command | Generates | Purpose |
| :--- | :--- | :--- |
| `3d:make-controller` | `Delivery/Controllers/*` | Receives requests and hands them to Use Cases. |
| `3d:make-request` | `Delivery/Requests/*` | Handles request input validation and authorization. |
| `3d:make-resource` | `Delivery/Resources/*` | Formats output for API responses, transforming Entities/Models to desired shapes. |
| `3d:make-route` | `Delivery/Routes/*` | Defines entry points (`web.php`, `api.php`) for the module. |
| `3d:make-view` | `Delivery/Views/*` | Blade templates scoped to and owned by the module. |

> **Note:** Commands use the `3d:` prefix, not `module:`.

---

## Technical Reference: The Controller Flow

Controllers in Morphling 3D modules should do as little as possible. Their only "knowledge" is which Use Case to call and how to relay the result.

```php
// modules/Transaction/Delivery/Controllers/TransactionController.php

public function store(CreateTransactionRequest $request, ProcessPaymentUseCase $useCase): JsonResponse 
{
    // 1. Convert the validated Request into a type-safe DTO
    $dto = TransactionDto::fromRequest($request);

    // 2. Pass DTO to the Application Layer via the Use Case
    $result = $useCase->execute($dto);

    // 3. Return a standardized API response
    return ApiResponse::success(
        $result['data'], 
        $result['message']
    );
}
```

---

## Request Validation & DTO Mapping

Use Laravel's `FormRequest` classes to separate validation from controllers. This keeps controller actions clear of validation clutter.

* **Validation:** Automatically performed by the FormRequest before your controller method is invoked.
* **DTO Conversion:** Use the `YourDto::fromRequest($request)` pattern to create the relevant DTO, containing only the data essential for business logic. This removes web-specific context (cookies, headers, etc.).

---

## View Namespacing

Morphling 3D automatically creates a custom view namespace for each module (e.g., `view('transaction::index')`). This ensures:

- No collisions between views across modules.
- UI assets are co-located with their owning logic.

```php
// Vanilla Laravel: view('index')
// Morphling 3D: view('transaction::index')
```

---

## Best Practices: The "Thin Controller" Rule

* **No Business Logic:** If you spot anything like `if ($balance > 0)` in your controller, move it to a Use Case (Application Layer) or Entity/Domain object (Domain Layer).
* **Single Responsibility:** Controllers should deal only with HTTP/request context: extracting and validating input, invoking Use Cases, and packaging output.
* **Resource Classes:** For complex outputs, always use API Resources (extends `JsonResource`) to handle transformation and avoid logic leaks into controllers.

---

## Troubleshooting

### "My routes aren't working!"
Ensure the Module's Service Provider—in the **Infrastructure** layer—properly loads your routes. In the `boot()` method, `registerRoutes()` must be called. If you've created new module files, run:

```bash
php artisan 3d:discover
```
to refresh all module registrations.

### "How do I share layouts between modules?"
Store project-wide layouts in Laravel's conventional `resources/views/layouts`, or centralize them in a `Shared` module. Then use:

```php
view('shared::layouts.app')
```
for layouts accessible from any module.