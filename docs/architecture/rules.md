# Architectural Guardrails & Rules

Morphling 3D enforces clear, strict architectural boundaries to support robust Domain-Driven Design (DDD) and Hexagonal Architecture. These aren’t just "best practices"—they are core requirements for keeping your application modular, scalable, and maintainable as it grows.

> **Core Principle**
> 
> **All dependencies must point inward to the Domain.**
> - The Domain must never depend on Infrastructure, Frameworks (like Laravel), or external systems.
> - Outer layers (Delivery, Infrastructure) can depend on inner layers (Domain, Application), but never the other way around.

---

## 1. Domain Layer: Pure Business Logic

**What it is:**  
The Domain layer (Entities, Value Objects, Enums, Domain Services, Interfaces) is the heart of your business logic.

- **Rule:** Absolutely no dependencies on Laravel, Eloquent, or framework-specific classes.
- **What’s allowed:** Only pure PHP and your own domain types. (Repository interfaces can be defined here—see below.)
- **What’s forbidden:**  
  - No references to `Illuminate\Http\Request`, `Eloquent\Model`, Laravel Facades, or database operations.
  - No awareness of HTTP, database, or infrastructure.
- **Why:** You should be able to copy the Domain untouched into any PHP project and have all core logic "just work."

---

## 2. Application Layer: Coordination & Use Cases

**What it is:**  
This layer contains Use Cases (application/business workflows), DTOs, and orchestrates the interaction between the Delivery and Domain.

- **Rule:**  
  - No direct access to Eloquent or the database.  
  - Never use `User::where(...)` or create new models directly!
- **How it works:**  
  - Handles business workflows by calling Repository **interfaces** defined in the Domain.  
  - Receives input as DTOs (Data Transfer Objects) and returns DTOs or Entities.
- **Why:** This enables easy testing (mocking repos), ensures business logic stays in Domain, and keeps Application free from infrastructure details.

---

## 3. Delivery Layer: Entry & Exit Points

**What it is:**  
Handles user interaction—Controllers, Routes, Views, API endpoints, CLI commands.

- **Rule:**  
  - No business logic.  
  - No direct dependency on Infrastructure or Eloquent models.
- **Responsibility:**  
  - Validate incoming requests (using FormRequest, etc.)
  - Convert inputs to DTOs.
  - Invoke Use Cases from the Application Layer.
  - Return responses (JsonResponse, View, etc.).
- **Why:** Keeps input/output logic decoupled from business logic, making your system flexible and testable.

---

## 4. Infrastructure Layer: Implementation Details

**What it is:**  
Persistence (Eloquent Models, Repositories), Integrations (APIs, Email, Stripe, etc.), Data Mappers.

- **Rule:**  
  - **Only** implements interfaces defined in the Domain.
  - Never contains business rules or workflow logic.
- **Allowed:**  
  - Works with Eloquent, raw SQL, queue drivers, 3rd-party SDKs, etc.—but must not leak details to Domain or Application.
- **Why:** You can swap Infrastructure (e.g. move from MySQL to MongoDB, or Stripe to another payment provider) with minimum disruption.

---

## Layer Dependency Table

| Layer           | Can Access                                | Must Not Access                                   |
|-----------------|-------------------------------------------|---------------------------------------------------|
| **Delivery**      | Application, Domain                      | Infrastructure                                    |
| **Application**   | Domain                                   | Delivery, Infrastructure (concrete/implementation) |
| **Domain**        | Itself (pure PHP, domain interfaces)     | Application, Delivery, Infrastructure             |
| **Infrastructure**| Domain (interfaces), itself              | Delivery, Application                             |

---

## Why These Guardrails Matter

1. **Stability** — Business rules survive tech changes (DB, UI, API gateways).
2. **Testability** — You can unit test Use Cases or Entities without a database or HTTP layer.
3. **Consistency** — Every module follows the same architectural flow, so developers can work in parallel and onboard rapidly.
4. **Maintainability** — Refactoring/fixing bugs or adding features remains manageable even as projects grow large.

---

## Enforcement Checklist

- [ ] **Entity** mentions a database table?  
      ⟶ Move that logic to an Eloquent Model in Infrastructure.

- [ ] **Controller** or **View** contains business rules/validation logic?  
      ⟶ Extract to a Use Case (Application) or Domain Service.

- [ ] **Use Case** directly creates or manipulates Eloquent/DB objects?  
      ⟶ It should only interact with Repository Interfaces.

- [ ] **Infrastructure** class implements business logic or coordinates workflow?  
      ⟶ Refactor logic to Application or Domain layer.

---

Follow these guardrails to keep your application decoupled, scalable, and easy to reason about as your team or project grows.