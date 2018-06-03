## Laravel 5 CRUD Generator 


This package generate CRUD(Create, Read, Update and Delete) files base database tables, to save time for programmer.


### Install

Require this package with composer using the following command:

```bash
composer require dweik/crud-generator
```

After updating composer, add the service provider to the `providers` array in `config/app.php`

```php
Dweik\CrudGenerator\CrudGeneratorServiceProvider::class,
```

To install this package on only development systems, add the `--dev` flag to your composer command:

```bash
composer require --dev dweik/crud-generator
```

In Laravel, instead of adding the service provider in the `config/app.php` file, you can add the following code to your `app/Providers/AppServiceProvider.php` file, within the `register()` method:

```php
public function register()
{
    if ($this->app->environment() !== 'production') {
        $this->app->register(\Dweik\CrudGenerator\CrudGeneratorServiceProvider::class);
    }
    // ...
}
```

This will allow your application to load the Laravel CRUD Generator on non-production environments.

### Automatic CRUD generation for Laravel

You can now generate the CRUD files by

```bash
php artisan crud:generate
```

The Laravel CRUD Generator is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)


### Changelog

v1.1.0
* Add executing Laravel authentication to the options
* Add executing Laravel migrate command to the options
* Ignore auth tables
* Ignore migration table
* Add Option for ignore create_at, updated_at and deleted_at columns
* Fix Routing service in all laravel versions +5.1

v1.0.1
* Fix bugs

V1.0.0 
* Support mysql, postgres and mysqli
* Generate Add/Edit Page with fields validation
* Use Javascript Validation for Numeric fields
* Add delete button in action field
* Generate Models for database tables   
* use namespace to generated files
* Ignore none auto-increment tables