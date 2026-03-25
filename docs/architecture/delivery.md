# Delivery

The **Delivery** layer is your module’s interface boundary: controllers, routes, requests, resources, and views.

The goal is to keep HTTP/UI concerns out of your Domain rules.

## What Morphling 3D Generates

- `module:make-controller` generates `Delivery/Controllers/*`
- `module:make-request` generates `Delivery/Requests/*`
- `module:make-resource` generates `Delivery/Resources/*`
- `module:make-route` generates route files under `Delivery/Routes/*`
- `module:make-view` generates Blade views under `Delivery/Views/*`

## Example: Controller → View Contract

The controller stub returns a namespaced view alias derived from the module name (excerpt):

```php
public function index(Request $request)
{
    return view('{{ module_snake }}::index');
}
```

## Navigation

- [Domain Responsibilities](#/architecture/domain)
- [Application Responsibilities](#/architecture/application)
- [Infrastructure Responsibilities](#/architecture/infrastructure)

