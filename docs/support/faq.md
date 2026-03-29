# Frequently Asked Questions (FAQ)

A comprehensive FAQ to accelerate your Morphling 3D journey. If you’re new to modular DDD in Laravel, start here!

---

### Q: **What is Morphling 3D?**

**A:** Morphling 3D is a modular scaffolding engine for Laravel that enforces Domain-Driven Design (DDD) by dividing your application into four clear layers: Delivery, Application, Domain, and Infrastructure.

---

### Q: **Can I use standard Laravel Models in my Use Case?**

**A:** You can, but you shouldn’t. Using Eloquent models in Use Cases tightly couples your business logic to your database, making testing and future refactoring harder. Always return a Domain Entity from your Repository.

---

### Q: **Where do I put third-party SDK logic (e.g., Stripe, AWS)?**

**A:** Put the raw SDK implementation inside `Infrastructure/External`. Then define an interface in the Domain layer to keep your application decoupled from the concrete provider.

---

### Q: **Is Morphling 3D compatible with Laravel Octane?**

**A:** Yes! The framework uses standard Service Providers for module discovery, so it is fully compatible with Laravel Octane’s high-performance event loop model.

---

### Q: **How do I add a new module?**

**A:** Run `php artisan morphling:make:module ModuleName`. This scaffolds the four essential layers with folders and boilerplate code.

---

### Q: **How do I map HTTP requests to Data Transfer Objects (DTOs)?**

**A:** Morphling 3D autogenerates a `fromRequest()` method on your DTOs. Use this in your controller to convert a request to a DTO in a type-safe way.

---

### Q: **How do I bind interfaces to implementations?**

**A:** In your module’s ServiceProvider (`Infrastructure/Providers`), add your bindings in the `register()` method using `$this->app->bind(Interface::class, Implementation::class);`

---

### Q: **I’m getting a “Class Not Found” error. What should I check?**

**A:** This usually means your PHP namespace doesn't match your file path. Double-check that the folder and the namespace declaration are in sync, then run `composer dump-autoload`.

---

### Q: **Why can’t Laravel find my module’s routes?**

**A:** Make sure the Service Provider is discovered and auto-registered. Run `php artisan module:discover` and check `bootstrap/providers.php` for your module’s provider.

---

### Q: **Can one Use Case call another Use Case?**

**A:** Technically, yes, but it’s not recommended. Shared or reusable logic should go into Domain Services or Domain Entities, not Use Cases.

---

### Q: **What is the ‘Shared’ kernel and when should I use it?**

**A:** The `modules/Shared` directory contains base classes and logic reusable across modules. Only move things here **after** you need them in two or more modules.

---

### Q: **How do I keep my Eloquent Models out of the Domain layer?**

**A:** Never return Eloquent models or raw database objects from your repositories—use Mappers to convert them into Domain Entities before crossing the Application boundary.

---

### Q: **Where should I add validation logic?**

**A:** Validation belongs in the Delivery layer (e.g., Form Requests) and should not leak into the Application/Domain layers. Your Domain can enforce business rules but not transport-level validation.

---

### Q: **How do I handle migrations in a module?**

**A:** Ensure you call `$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');` in your ServiceProvider, then run `php artisan migrate` as usual.

---

### Q: **Why is my repository returning null when the database has data?**

**A:** Check your Infrastructure Mapper. If it fails to transform the database row to a Domain Entity, the Repository will return null by design.

---

### Q: **Can I use global helpers or traits?**

**A:** Yes, but place them in `modules/Shared/Infrastructure/Traits` to avoid duplication and maintain modularity.

---

### Q: **How do I write unit tests for modules?**

**A:** Since your Domain Entities and Use Cases are decoupled from Laravel and the database, you can easily unit test them—just mock the interfaces and dependencies.

---

### Q: **How can I reset Morphling’s state if something is broken?**

**A:** Run the following:
```bash
composer dump-autoload
php artisan module:discover
php artisan config:clear
php artisan route:clear
```

---

### Q: **Can I customize folder names or module locations?**

**A:** Yes. Edit `config/morphling.php` to set custom paths, namespaces, or enable/disable features like auto-discovery.

---

### Q: **How do I avoid circular dependencies between modules?**

**A:** Extract shared logic into the `Shared` kernel or use domain events instead of direct inter-module calls. Never let modules depend mutually on each other.
