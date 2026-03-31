# **Refactoring: From MVC to DDD with Morphling 3D**

Migrating from a traditional "Fat Controller/Fat Model" Laravel architecture to the **Morphling 3D** Domain-Driven Design approach can seem daunting. However, you do **not** need to rewrite your entire application at once—a feature-by-feature migration is safer and more effective. Below is a practical, incremental refactoring process inspired by Morphling 3D's layered architecture.

---

## **Why "Fat" Controllers Hurt**

In a typical Laravel MVC app, business logic, persistence, validation, and side effects often get jumbled within controllers, like so:

```php
// app/Http/Controllers/OrderController.php
public function store(Request $request) {
    $request->validate([...]); // 1. Validation
    $order = Order::create([...]); // 2. Database Persistence
    if ($order->amount > 1000) { ... } // 3. Business Rules
    Mail::to($request->user())->send(new OrderPlaced($order)); // 4. Side Effects
    return response()->json($order); // 5. Response
}
```

While expedient, this "everything in one place" style leads to tightly coupled, hard-to-test, and hard-to-scale systems.

---

## **The 5-Step Extraction for DDD**

Morphling 3D enforces a structure based on 4 essential layers: Delivery, Application, Domain, and Infrastructure. Here’s how to gradually extract responsibilities from a bloated controller:

### **Step 1: Move Validation to Delivery**

Create a dedicated `FormRequest` in the module’s `Delivery/Requests` directory for validation concerns.

* **Command:** `php artisan 3d:make-request StoreOrder Order`
* **Benefit:** Controllers only receive already-validated input. They no longer deal with validation details.

### **Step 2: Introduce a DTO for Data Transfer**

Construct a `DTO` (Data Transfer Object) in `Application/DTOs` to represent the request data in a type-safe, intent-driven structure.

* **Command:** `php artisan 3d:make-dto Order Order`
* **Benefit:** Removes raw request data handling from your business logic. Data is now a clean, immutable PHP object.

### **Step 3: Move Business Rules into the Domain Layer**

Extract business logic (e.g., the `if` rule) into a Domain Entity under `Domain/Entities`.

* **Command:** `php artisan 3d:make-entity Order Order`
* **Benefit:** Example: the logic `if ($order->amount > 1000)` may become an entity method like `$order->isHighValue()`, enabling independent, database-free domain testing.

### **Step 4: Move Persistence to an Infrastructure Repository**

Relocate model persistence (e.g., `Order::create()`) into an infrastructure repository class in `Infrastructure/Repositories`.

* **Command:** `php artisan 3d:make-repo Order Order`
* **Benefit:** The app talks to repositories instead of Eloquent/DB directly—code depends on interfaces, not implementations.

### **Step 5: Orchestrate in an Application Use Case**

Bring everything together by implementing a Use Case under `Application/UseCases`. This orchestrates validation, business rules, persistence, and side effects.

* **Command:** `php artisan 3d:make-usecase StoreOrder Order`
* **Benefit:** The use case serves as your application/interactor boundary. Controllers just dispatch data to it.

---

## **How the Refactored Controller Looks**

After extracting layers, your controller can be as thin as:

```php
// modules/Order/Delivery/Controllers/OrderController.php
public function store(StoreOrderRequest $request, StoreOrderUseCase $useCase) {
    $dto = OrderDto::fromRequest($request);
    $result = $useCase->execute($dto);

    return ApiResponse::success($result['data'], $result['message']);
}
```
- **No business or DB logic.**
- **No model instantiation in the controller.**
- **Testable and predictable.**

---

## **Why This Layered Refactor Matters**

* **Incremental Adoption:** You don't need to migrate everything at once. You can continue using MVC for most features while refactoring critical or complex flows (like Payments or Shipping) into Morphling 3D modules.
* **Better Testability:** Once code is extracted from controllers into DTOs, Use Cases, and Domain Entities, you can finally write realistic **unit tests**—even for complex business rules.
* **Improved Maintainability:** With true separation of concerns, questions of "where does this logic go?" are answered by convention.

---

## **Migration Strategy: The "Strangler Fig" Pattern**

1. Identify a single, high-impact feature.
2. Create a dedicated Morphling 3D **module** for it.
3. Build out its four layers incrementally (Delivery, Application, Domain, Infrastructure).
4. Point existing routes to your new, layered controller in the `Delivery` layer.
5. Once migrated and tested, remove the old MVC code.

---

**Remember:** Migrating to DDD with Morphling 3D is not about "rewriting everything," but about **gaining clarity and maintainability, one business feature at a time**.