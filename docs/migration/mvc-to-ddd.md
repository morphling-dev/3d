# **Refactoring: From MVC to DDD**

The transition from a standard "Fat Controller/Fat Model" Laravel setup to **Morphling 3D** can feel overwhelming. The key is to avoid a "Big Bang" rewrite. Instead, refactor one feature at a time using the **Extraction Method**.

---

## **The Anatomy of a "Fat" Controller**
In a traditional MVC setup, your controller often looks like this:

```php
// App/Http/Controllers/OrderController.php
public function store(Request $request) {
    $request->validate([...]); // 1. Validation
    $order = Order::create([...]); // 2. Persistence
    if ($order->amount > 1000) { ... } // 3. Business Logic
    Mail::to($request->user())->send(new OrderPlaced($order)); // 4. Side Effects
    return response()->json($order); // 5. Response
}
```

---

## **The 5-Step Extraction Process**

### **Step 1: Move Validation to Delivery**
Create a `FormRequest` in the module's `Delivery/Requests` folder.
* **Command:** `php artisan module:make-request StoreOrder Order`
* **Result:** Your controller no longer cares *how* the data is validated; it just receives valid data.

### **Step 2: Move Data to a DTO**
Create a DTO in `Application/DTOs`.
* **Command:** `php artisan module:make-dto Order Order`
* **Result:** You stop passing the "Request" object into your logic. You pass a clean, `readonly` PHP object instead.

### **Step 3: Move Logic to an Entity**
Identify the business rules (the `if` statements) and move them into a `Domain/Entity`.
* **Command:** `php artisan module:make-entity Order Order`
* **Result:** The logic `if ($order->amount > 1000)` becomes a method like `$order->isHighValue()`. This is now testable without a database.

### **Step 4: Move Persistence to a Repository**
Move the `Order::create()` call into an `Infrastructure/Repository`.
* **Command:** `php artisan module:make-repo Order Order`
* **Result:** Your application doesn't know it's using Eloquent. It just knows it's "saving" an Order.

### **Step 5: Orchestrate with a Use Case**
Finally, tie it all together in an `Application/UseCase`.
* **Command:** `php artisan module:make-usecase StoreOrder Order`

---

## **The Final Result (The "Thin" Controller)**

After refactoring, your `OrderController` looks like this:

```php
// modules/Order/Delivery/Controllers/OrderController.php
public function store(StoreOrderRequest $request, StoreOrderUseCase $useCase) {
    $dto = OrderDto::fromRequest($request);
    $result = $useCase->execute($dto);
    
    return ApiResponse::success($result['data'], $result['message']);
}
```

---

## **Why This Matters**

* **Incremental Progress:** you can keep 90% of your app in standard MVC while moving the most critical, complex features (like Payments or Shipping) into Morphling 3D modules.
* **Testing:** Once a feature is moved, you can finally write **Unit Tests** for it, which was likely impossible when it was buried in a Fat Controller.
* **Mental Clarity:** By separating "How I save it" (Infrastructure) from "What are the rules" (Domain), your code becomes much easier to reason about.

---

## **Migration Strategy: "The Strangler Fig"**
1. Identify a single, high-value feature.
2. Create the new Module folder.
3. Build the layers as described above.
4. Point your existing routes to the new **Delivery** Controller.
5. Delete the old MVC Controller and logic.