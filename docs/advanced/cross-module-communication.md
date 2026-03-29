# **Cross-Module Communication**

In a modular architecture, the biggest challenge is keeping modules **decoupled**. If the `Ordering` module directly calls a class inside the `Accounting` module, you've created a "Distributed Monolith" where you can't change one without breaking the other.

Morphling 3D promotes three specific patterns for inter-module communication, ranked from "Loose" to "Strict."

---

## **1. Domain Events (The Recommended Way)**
This is the "Fire and Forget" approach. Module A announces that something happened, and any other module can choose to listen and react.

* **Mechanism:** Laravel's native Event/Listener system.
* **The Rule:** The **Event** is defined in the source module's Domain. The **Listener** is defined in the target module's Infrastructure.



**Example Scenario: Order Placed -> Notify Warehouse**
1.  **Ordering Module:** Dispatches `OrderPlacedEvent` from its Use Case.
2.  **Warehouse Module:** Has a Listener in `Infrastructure/Listeners/PrepareShipment.php` that waits for that event.

---

## **2. Shared Kernel (The "Common Ground" Way)**
If two modules need to share a specific data structure or a contract (Interface), that contract must live in `modules/Shared`.

* **Mechanism:** Interfaces or Value Objects in the Shared Kernel.
* **The Rule:** Module A and Module B both depend on `Shared`. Neither depends on each other.

**Example Scenario: Shared Money Logic**
Instead of `Payroll` and `Invoicing` both creating their own "Currency" logic, they both use `Modules\Shared\Domain\ValueObjects\Money`.

---

## **3. Service Interfaces (The "Direct but Decoupled" Way)**
Sometimes, Module A *needs* a specific piece of data from Module B immediately (Synchronous). Instead of calling Module B's Eloquent Model, Module A calls an **Interface** defined in the Shared Kernel.

* **Mechanism:** Interface in `Shared`, Implementation in `Module B`.
* **The Rule:** Module A only knows about the Interface. It has no idea how Module B actually retrieves the data.



**Example Scenario: Checking User Balance**
1.  **Shared Kernel:** Defines `UserBalanceProviderInterface`.
2.  **Wallet Module:** Implements that interface in its Infrastructure layer.
3.  **Shop Module:** Injects the Interface into its Use Case.

---

## **Why This Matters**

1.  **Independence:** You can rewrite the entire `Warehouse` module (even move it to a different database) and the `Ordering` module won't notice, as long as the Event name stays the same.
2.  **Side-Effect Management:** If sending an email fails in the `Notification` module, it doesn't crash the `Checkout` process in the `Shop` module.
3.  **Parallel Development:** Two teams can work on different modules simultaneously without stepping on each other's code.

---

## **Best Practices**

* **Avoid "God Modules":** If every module depends on the `User` module, your architecture is likely a "Star" topology (brittle). Use IDs (strings/integers) to link data across modules instead of Object References.
* **Async by Default:** Use Laravel's `ShouldQueue` on your Listeners whenever possible. This keeps your user-facing requests fast.
* **Data Duplication is Okay:** In DDD, it is often better to have a small "cached" version of a User's name in the `Order` table than to join across 5 module tables every time you load a list.