# Architectural Guardrails & Rules

To maintain the integrity of a **Domain-Driven Design (DDD)** system, Morphling 3D enforces strict boundaries. These rules are not just "suggestions"—they are the technical requirements that prevent your project from turning into a "Big Ball of Mud" as it scales.

> [!IMPORTANT]
> **Core Principle:** Dependencies must always point **inwards** toward the Domain. The Domain should never know about the Database, the Web, or the Framework.

---

## 1. Domain: The "Framework-Free" Zone
The **Domain Layer** (Entities, Value Objects, Enums) is the heart of your application.

* **Rule:** Zero dependency on Laravel. You should be able to copy your Domain folder into a non-Laravel project and have the logic still work.
* **Forbidden:** No `Illuminate\Http\Request`, no Eloquent Models, and no Facades (e.g., `DB::table`).
* **Exception:** For practical reasons, generated repository stubs may use Laravel's `Collection` or `Paginator` for data structures.

---

## 2. Application: The Orchestrator
The **Application Layer** (Use Cases, DTOs) acts as the bridge.

* **Rule:** No Eloquent Queries. A Use Case should never call `User::where('id', 1)->first()`.
* **The Workflow:** It must only talk to **Repository Interfaces**. It asks for an Entity, manipulates it, and asks the Repository to save it.
* **Data Handling:** All data entering this layer must be wrapped in a **DTO**.

---

## 3. Delivery: The Entry & Exit
The **Delivery Layer** (Controllers, Routes, Views) handles the "outside world."

* **Rule:** No Business Logic. If you have an `if` statement checking a user's balance or status in a Controller, you are breaking the pattern.
* **Responsibility:** Validate the input via `FormRequest`, convert it to a `DTO`, call the `UseCase`, and return a `JsonResponse` or `View`.

---

## 4. Infrastructure: The Implementation
The **Infrastructure Layer** (Repositories, Mappers, External APIs) handles the "how."

* **Rule:** Implementation, not Innovation. This layer should only implement the contracts (Interfaces) defined by the Domain.
* **Responsibility:** This is the *only* place where Eloquent models, SQL queries, or Third-Party SDKs (like Stripe or AWS) should live.

---

## Dependency Rule Summary

| From Layer | Can Access | Cannot Access |
| :--- | :--- | :--- |
| **Delivery** | Application, Domain | Infrastructure |
| **Application** | Domain | Delivery, Infrastructure (Concrete) |
| **Domain** | Nothing (Pure) | Delivery, Application, Infrastructure |
| **Infrastructure** | Domain | Delivery, Application |



---

## Why These Rules Matter

1.  **Stability:** Your business rules (Domain) don't change just because you switched from MySQL to MongoDB (Infrastructure).
2.  **Testability:** You can unit test a `UseCase` by mocking a `RepositoryInterface` without ever touching a database.
3.  **Parallel Development:** A Senior Dev can write the `Domain` logic while a Junior Dev builds the `Delivery` (UI/API) layer simultaneously.

---

## Enforcement Checklist
- [ ] Does my **Entity** mention a database table? (If yes, move it to a Model).
- [ ] Does my **Controller** have more than 10 lines of code? (If yes, move logic to a Use Case).
- [ ] Is my **Use Case** using the `new` keyword for an Eloquent Model? (If yes, use a Repository).