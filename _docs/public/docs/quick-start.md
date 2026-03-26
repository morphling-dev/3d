# Quick Start

## 1. Install

Install the package using Composer and run the installation command:

```bash
composer require morphling-dev/3d
php artisan 3d:install
```

Next, ensure that `"Modules\\"` is registered in the `composer.json` file of your app under the PSR-4 autoload section. After that, rebuild the autoloader:

```bash
composer dump-autoload
```

## 2. Create a Module

To create a new module, run:

```bash
php artisan module:new Transaction
```

## 3. Discover Modules (Auto-wiring)

Register and auto-wire your modules by running:

```bash
php artisan module:discover
```

## 4. Run the Application

Start your application with:

```bash
php artisan serve
```

The module route will be generated under `modules/Transaction/Delivery/Routes/`. You can test your new module from there.
