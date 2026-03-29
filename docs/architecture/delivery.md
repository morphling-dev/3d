# The Delivery Layer: The Interface Boundary

The **Delivery Layer** is the "Front Desk" of your module. It is the only layer that interacts with the outside world—whether that’s a web browser, a mobile app via an API, or a developer using a CLI command. Its primary mission is to translate incoming requests into a language the internal layers understand, and then format the internal results into a response the user expects.

---

## Executive Summary
In Morphling 3D, the Delivery layer is strictly responsible for **Input** and **Output**. It handles routing, middleware, request validation, and response formatting (JSON, Blade views, or redirects). It must never contain business logic.

> [!NOTE]
> **Status:** `Interface` | **Dependency:** `Depends on Application`

---

## Key Concepts: The "Translator" Model
Think of the Delivery layer as a high-end concierge. They listen to the guest's request (The HTTP Request), verify their credentials (Validation), fill out a standard internal form (The DTO), and hand it to the manager (The Use Case). When the manager is done, the concierge presents the result beautifully to the guest.

---

## What Morphling 3D Generates

The engine provides generators for every common entry and exit point:

| Command | Generates | Purpose |
| :--- | :--- | :--- |
| `module:make-controller` | `Delivery/Controllers/*` | Directs traffic and maps Requests to Use Cases. |
| `module:make-request` | `Delivery/Requests/*` | Handles authorization and input validation. |
| `module:make-resource` | `Delivery/Resources/*` | Transforms Entities/Models into specific JSON shapes. |
| `module:make-route` | `Delivery/Routes/*` | Defines the `web.php` or `api.php` endpoints. |
| `module:make-view` | `Delivery/Views/*` | Blade templates namespaced to the module. |

---

## Technical Reference: The Controller Flow

A clean Morphling 3D controller should be remarkably short. Its only "intelligence" is knowing which Use Case to call and how to return the data.

```php
### modules/Transaction/Delivery/Controllers/TransactionController.php

public function store(CreateTransactionRequest $request, ProcessPaymentUseCase $useCase): JsonResponse 
{
    // 1. Convert validated Request into a type-safe DTO
    $dto = TransactionDto::fromRequest($request);
    
    // 2. Execute the Use Case (Application Layer)
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

By using `FormRequest` classes, you keep your controllers from becoming cluttered with validation rules.

* **Validation:** Happens automatically before the controller method is even hit.
* **DTO Conversion:** The `TransactionDto::fromRequest($request)` method is the bridge. It strips away the "Web" context (cookies, headers, session) and keeps only the raw data needed for the business logic.

---

## View Namespacing

Morphling 3D automatically registers a view namespace for every module. This avoids name collisions across your application.

```php
// Standard Laravel: view('index')
// Morphling 3D: view('transaction::index')
```

This allows you to organize your UI components alongside the logic they represent, making the module truly self-contained.

---

## Best Practices: The "Thin Controller" Rule

* **Zero Logic:** If you see an `if ($balance > 0)` in your controller, it's a red flag. That belongs in the **Domain**.
* **One Responsibility:** The controller should only handle HTTP-related tasks (setting headers, status codes, and redirects).
* **Use Resources:** For complex API outputs, always use `JsonResource` to keep your output transformation logic separate from your controller.

---

## Troubleshooting

### "My routes aren't working!"
Check your Module's **Service Provider** in the Infrastructure layer. Ensure `registerRoutes()` is being called in the `boot()` method. If you just added the file, run `php artisan module:discover`.

### "How do I share layouts between modules?"
Keep your global layouts in the standard Laravel `resources/views/layouts` folder or create a `Shared` module view namespace (e.g., `view('shared::layouts.app')`).