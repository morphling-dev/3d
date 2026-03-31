# Frequently Asked Questions (FAQ)

A comprehensive FAQ to accelerate your Morphling 3D journey. If you’re new to modular DDD in Laravel, start here!

---

### Q: **What is Morphling 3D?**

**A:** Morphling 3D is an architectural framework for Laravel that helps you structure large applications using Domain-Driven Design (DDD) and Hexagonal Architecture. It enforces separation into four main layers: Delivery, Application, Domain, and Infrastructure to reduce coupling and improve maintainability.

---

### Q: **Can I use standard Laravel Models (Eloquent) in my Use Case?**

**A:** You technically can, but it is not recommended. Using Eloquent models directly in Use Cases couples your business logic to your database and Laravel’s ORM, making testing and future refactoring much harder. Instead, always have your Repository return Domain Entities to your Application layer.

---

### Q: **Where do I put third-party SDK logic (e.g., Stripe, AWS)?**

**A:** Place the SDK integration in `Infrastructure/External`. Then, define an interface for this integration in your Domain layer and depend on the interface in your business logic to keep your Domain decoupled from infrastructure details.

---

### Q: **Is Morphling 3D compatible with Laravel Octane?**

**A:** Yes. Morphling 3D uses standard Laravel Service Providers for module discovery and is fully compatible with high-performance event loop environments like Laravel Octane.

---

### Q: **How do I add a new module?**

**A:** Run `php artisan 3d:new {ModuleName}`. This will scaffold the four architectural layers with the appropriate folders and boilerplate code for your new module.

---

### Q: **How do I map HTTP requests to Data Transfer Objects (DTOs)?**

**A:** Morphling 3D provides a consistent DTO pattern and conventionally includes a `fromRequest()` method on DTOs. In your Controller (Delivery Layer), call `YourDto::fromRequest($request)` to safely map a request to a DTO instance.

---

### Q: **How do I bind interfaces to implementations?**

**A:** In your module’s ServiceProvider (typically under `Infrastructure/Providers`), add your bindings in the `register()` method using `$this->app->bind(Interface::class, Implementation::class);`

---

### Q: **I’m getting a “Class Not Found” error. What should I check?**

**A:** This usually means your PHP namespace doesn't match your file path. Double-check that the folder and namespace declarations are in sync, then run `composer dump-autoload` to refresh class mappings.

---

### Q: **Why can’t Laravel find my module’s routes?**

**A:** Ensure the module’s Service Provider is auto-discovered and registered. Run `php artisan 3d:discover` and check that your module provider appears in the provider discovery output.

---

### Q: **Can one Use Case call another Use Case?**

**A:** While technically possible, it is discouraged. Reusable or shared logic should be placed in Domain Services or Entities—not inside Use Cases, to avoid coupling and keep Use Cases independent.

---

### Q: **What is the ‘Shared’ kernel and when should I use it?**

**A:** The `modules/Shared` directory acts as the Shared Kernel for base classes, objects, and logic reused across multiple modules. Only promote code here if it’s needed in two or more modules to avoid unnecessary dependencies.

---

### Q: **How do I keep my Eloquent Models out of the Domain layer?**

**A:** Never expose Eloquent models or database objects to your Domain or Application layers. Use a Mapper (typically in Infrastructure) to convert Eloquent data into pure Domain Entities before returning them upstream.

---

### Q: **Where should I add validation logic?**

**A:** Validation that checks incoming request parameters or shapes belongs to the Delivery layer (e.g., Form Requests or Request classes). Business rules can be enforced in the Domain, but never leak transport or HTTP concerns outside Delivery.

---

### Q: **How do I handle migrations in a module?**

**A:** In your module’s Service Provider, register migrations using `$this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');` and then run `php artisan 3d:migrate` (or `php artisan migrate`) as usual.

---

### Q: **Why is my repository returning null when the database has data?**

**A:** If your Infrastructure Mapper fails to convert a database row into a Domain Entity (for example, due to missing or invalid data), the Repository will return null by design. Double-check your Mapper and input data.

---

### Q: **Can I use global helpers or traits?**

**A:** Yes, but to keep things modular and avoid duplication, place shared helpers and traits in `modules/Shared/Infrastructure/Traits`.

---

### Q: **How do I write unit tests for modules?**

**A:** Because Use Cases and Domain Entities are decoupled from Laravel and the database, you can unit test them in isolation by mocking interfaces and dependencies. This makes modular testing much easier.

---

### Q: **How can I reset Morphling’s state if something is broken?**

**A:** Run the following:
```bash
composer dump-autoload
php artisan 3d:discover
php artisan config:clear
php artisan route:clear
```

---

### Q: **Can I customize folder names or module locations?**

**A:** Yes. Edit `config/3d.php` to configure custom paths, base namespaces, or to turn features like auto-discovery on or off.

---

### Q: **How do I avoid circular dependencies between modules?**

**A:** Move truly shared logic to the Shared kernel (`modules/Shared`) or use Domain Events for cross-module communication. Never make modules depend on each other directly; maintain strict modular boundaries.
