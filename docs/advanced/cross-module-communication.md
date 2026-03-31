# **Cross-Module Communication**

One of the central challenges in modular, DDD-driven architectures is maintaining **loose coupling** between modules. If the `Ordering` module directly depends on classes from the `Accounting` module, you risk creating a fragile, distributed monolith—where changes in one module ripple across the entire system.

Morphling 3D enforces decoupling and clear module boundaries via three primary patterns for inter-module communication, ordered from *most loosely coupled* to *most explicit contract*.

---

## **1. Domain Events (Recommended Pattern)**

The preferred method for module-to-module signaling is *Domain Events*. This is a "fire-and-forget" approach: Module A emits an event to announce something of interest has happened; any other module can listen and react independently.

* **Mechanism:** Leverages Laravel's native Event/Listener system.
* **Conventions:**
    - The **Event** class is defined in the *Domain* of the originating module.
    - **Listeners** are defined in the *Infrastructure* layer of any module that cares about the event (including the originating module, if desired).

**Example Scenario: Order Placed ➔ Notify Warehouse**

1.  **Ordering Module:** A UseCase dispatches `OrderPlaced` event from its Domain layer.
2.  **Warehouse Module:** Defines a Listener (e.g. `Infrastructure/Listeners/PrepareShipment.php`) that reacts to `OrderPlaced`.

---

## **2. Shared Kernel Contracts ("Common Language")**

For shared value objects, enums, or interfaces required by multiple modules, Morphling 3D uses the *Shared Kernel* pattern. These shared contracts reside in the `modules/Shared` directory and are treated as stable APIs.

* **Mechanism:** Place shared Interfaces or ValueObjects under Shared Kernel.
* **Conventions:**
    - Any module requiring the shared contract depends on `Shared`.
    - No module is allowed to depend directly on another module's code.

**Example Scenario: Shared Money Value Object**

If both `Payroll` and `Invoicing` modules need "Money" logic, both utilize `Modules\Shared\Domain\ValueObjects\Money`—not their own ad-hoc versions.

---

## **3. Service Interfaces ("Direct & Decoupled")**

Occasionally, a module needs synchronous, contract-based communication with another (for example, fetching current data). Instead of directly calling into another module’s logic, the calling module depends on an **interface** defined in Shared, which is then implemented by the target module.

* **Mechanism:** Service Interface in Shared; concrete implementation in the "provider" module’s Infrastructure layer.
* **Conventions:**
    - Module A depends only on the Interface.
    - Module B provides the implementation and registers it via Laravel’s container (often using service providers or auto-discovery).

**Example Scenario: User Balance Lookup**

1.  **Shared Kernel:** Defines `UserBalanceProviderInterface`.
2.  **Wallet Module:** Implements that interface in its Infrastructure layer.
3.  **Shop Module:** Type-hints the Interface in UseCases or Services—never referencing the implementation or third-party modules directly.

---

## **Why Use These Patterns?**

1.  **Decoupling:** Individual modules become swappable or refactorable without system-wide side effects. For example, you can swap out the `Warehouse` module’s technology stack, and `Ordering` remains unaffected (so long as contracts are honored).
2.  **Fault Isolation:** Side-effects (such as failed email sending in a `Notifications` module) don't break the main user flow (`Shop` or `Checkout`).
3.  **Independent Development:** Teams can build, refactor, or deploy modules in parallel, avoiding entanglement and conflicts.

---

## **Best Practices**

* **Resist "God Modules":** If most modules depend on a single "hub" module (like `User`), you’re likely building a fragile central point of failure. Prefer referencing IDs (e.g., strings, integers) between modules, rather than object references.
* **Prefer Asynchronous:** Implement Laravel's `ShouldQueue` interface on Listeners where feasible—decoupling user experience from cross-module processing.
* **Embrace Strategic Duplication:** It’s perfectly fine for a module’s table (like `Orders`) to store a cached username or email. This avoids N+1 cross-module lookups and honors DDD boundaries. Data duplication, in small amounts, can be a feature for decoupled design.