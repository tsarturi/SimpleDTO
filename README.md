<div align="center">
    <p>
        <h1>SimpleDTO</h1>
        Simple Data Transfer Objects with FormRequest Validation for Laravel applications.
    </p>
    <p>This library was based on https://github.com/WendellAdriel/laravel-validated-dto .</p>
</div>

<p align="center">
<a href="https://packagist.org/packages/tsarturi/simpledto"><img src="https://img.shields.io/packagist/v/tsarturi/simpledto.svg?style=flat-square" alt="Packagist"></a>
<a href="https://packagist.org/packages/tsarturi/simpledto"><img src="https://img.shields.io/packagist/php-v/tsarturi/simpledto.svg?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://packagist.org/packages/tsarturi/simpledto"><img src="https://img.shields.io/badge/Laravel-9.x,%2010.x-brightgreen.svg?style=flat-square" alt="Laravel Version"></a>
</p>

<p align="center">
    <a href="#features">Features</a> |
    <a href="#installation">Installation</a> |
    <a href="#configuration">Configuration</a> |
    <a href="#generating-dtos">Generating DTOs</a> |
    <a href="#generating-formrequest">Generating FormRequest</a>
</p>


## Features

Features:

- Allows the creation of DTO's easily and quickly.
- Easy integration into your existing projects.
- Validation of the data in the same way that it validates the **Request**.
- Use **FormRequest** for validation and authorization.
- Custom validation messages.
- Easy data conversion.
- Definition of default values.

## Installation

```
composer require tsarturi/simpledto
```

## Configuration

Publish the config file:

```
php artisan vendor:publish --provider="Tsarturi\SimpleDTO\Providers\SimpleDTOServiceProvider" --tag=config
```

## Generating DTOs

You can create DTOs using the `make:simpledto` command:

```
php artisan make:simpledto UserDTO
```

It's create an UserDTO class into App\DTOs folder.

## Generating FormRequest

You can create FormRequest class using the `make:simpledtoformrequest` command:

```
php artisan make:simpledtoformrequest UserStoreRequest
```

It's create an UserStoreRequest into Form Request's folder

## Using DTO's

```php
<?php

$dto = new UserDTO( [ 'name' => 'name', 'email' => 'email@email.com']);

```

## Using DTO's with FormRequest

```php
<?php

$dto = new UserDTO( [ 'name' => 'name', 'email' => 'email@email.com'], UserStoreRequest::class);

```
