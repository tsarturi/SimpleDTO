<div align="center">
    <p>
        <h1>SimpleDTO</h1>
        Simple Data Transfer Objects with FormRequest Validation for Laravel applications.
        This library is based on https://github.com/WendellAdriel/laravel-validated-dto
    </p>
</div>

<p align="center">
<a href="https://packagist.org/packages/tsarturi/simpledto"><img src="https://img.shields.io/packagist/v/tsarturi/simpledto.svg?style=flat-square" alt="Packagist"></a>
<a href="https://packagist.org/packages/tsarturi/simpledto"><img src="https://img.shields.io/packagist/php-v/tsarturi/simpledto.svg?style=flat-square" alt="PHP from Packagist"></a>
<a href="https://packagist.org/packages/tsarturi/simpledto"><img src="https://img.shields.io/badge/Laravel-9.x,%2010.x-brightgreen.svg?style=flat-square" alt="Laravel Version"></a>
</p>

<p align="center">
    <a href="#features">Features</a> |
    <a href="#installation">Installation</a> |
    <a href="#generating-dtos">Generating DTOs</a> |
    <a href="#credits">Credits</a> |
    <a href="#contributing">Contributing</a>
</p>


## Features

- Easily integrate it with your current project
- Data validation the same way you validate a **Request**
- Easily define **custom validation messages**
- Support for **typed properties**
- **Type Casting** out-of-the-box for your DTOs properties
- Support casting of **nested data**
- Easily create **custom Type Casters** for your own needs

## Installation

```
composer require tsarturi/simpledto
```

## Configuration

Publish the config file:

```
php artisan vendor:publish --provider="Tsarturi\SimpleDTO\Providers\SimpleDTOServiceProvider" --tag=config
```

The configuration file will look like this:

## Why use this package

**Data Transfer Objects (DTOs)** are objects that are used to transfer data between systems. **DTOs** are typically used in applications to provide a simple, consistent format for transferring data between different parts of the application, such as **between the user interface and the business logic**.

This package provides a base **DTO Class** that can **validate** the data when creating a **DTO**. But why should we do this instead of using the standard **Request** validation?

Imagine that now you want to do the same action that you do in an endpoint on a **CLI** command for example. If your validation is linked to the Request you'll have to implement the same validation again.

With this package you **define the validation once** and can **reuse it where you need**, making your application more **maintainable** and **decoupled**.

## Generating DTOs

You can create `DTOs` using the `make:dto` command:

```
php artisan make:dto UserDTO
```

The `DTOs` are going to be created inside `app/DTOs`.

## Defining DTO Properties

You can define typed properties in your `DTO` outside the constructor:

```php
class UserDTO extends ValidatedDTO
{
    public string $name;

    public string $email;

    public string $password;
}
```

Remember that the property types must be compatible with the **Cast Type** you define for them.

## Defining Validation Rules

You can validate data in the same way you validate `Request` data:

```php
class UserDTO extends ValidatedDTO
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }
}
```

## Creating DTO instances

You can create a `DTO` instance on many ways:

### From arrays

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);
```

You can also use the `fromArray` static method:

```php
$dto = UserDTO::fromArray([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);
```

### From JSON strings

```php
$dto = UserDTO::fromJson('{"name": "John Doe", "email": "john.doe@example.com", "password": "s3CreT!@1a2B"}');
```

### From Request objects

```php
public function store(Request $request): JsonResponse
{
    $dto = UserDTO::fromRequest($request);
}
```

### From Eloquent Models

```php
$user = new User([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto = UserDTO::fromModel($user);
```

Beware that the fields in the `$hidden` property of the `Model` won't be used for the `DTO`.

### From Artisan Commands

You have three ways of creating a `DTO` instance from an `Artisan Command`:

#### From the Command Arguments

```php
<?php

use App\DTOs\UserDTO;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user {name} {email} {password}';

    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    public function handle()
    {
        $dto = UserDTO::fromCommandArguments($this);
    }
}
```

#### From the Command Options

```php
<?php

use App\DTOs\UserDTO;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user { --name= : The user name }
                                        { --email= : The user email }
                                        { --password= : The user password }';

    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    public function handle()
    {
        $dto = UserDTO::fromCommandOptions($this);
    }
}
```

#### From the Command Arguments and Options

```php
<?php

use App\DTOs\UserDTO;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'create:user {name}
                                        { --email= : The user email }
                                        { --password= : The user password }';

    protected $description = 'Create a new User';

    /**
     * Execute the console command.
     *
     * @return int
     *
     * @throws ValidationException
     */
    public function handle()
    {
        $dto = UserDTO::fromCommand($this);
    }
}
```

## Accessing DTO data

After you create your `DTO` instance, you can access any properties like an `object`:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto->name; // 'John Doe'
$dto->email; // 'john.doe@example.com'
$dto->password; // 's3CreT!@1a2B'
```

If you pass properties that are not listed in the `rules` method of your `DTO`, this data will be ignored and won't be available in your `DTO`:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
    'username' => 'john_doe', 
]);

$dto->username; // THIS WON'T BE AVAILABLE IN YOUR DTO
```

## Defining Default Values

Sometimes we can have properties that are optional and that can have default values. You can define the default values for
your `DTO` properties in the `defaults` function:

```php
class UserDTO extends ValidatedDTO
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email'],
            'username' => ['sometimes', 'string'],
            'password' => [
                'required',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }
    
    /**
     * Defines the default values for the properties of the DTO.
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [
            'username' => Str::snake($this->name),
        ];
    }
}
```

With the `DTO` definition above you could run:

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B'
]);

$dto->username; // 'john_doe'
```

## Converting DTO data

You can convert your DTO to some formats:

### To array

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toArray();
// [
//     "name" => "John Doe",
//     "email" => "john.doe@example.com",
//     "password" => "s3CreT!@1a2B",
// ]
```

### To JSON string

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toJson();
// '{"name":"John Doe","email":"john.doe@example.com","password":"s3CreT!@1a2B"}'

$dto->toJson(true); // YOU CAN CALL IT LIKE THIS TO PRETTY PRINT YOUR JSON
$dto->toPrettyJson(); // OR LIKE THIS
// {
//     "name": "John Doe",
//     "email": "john.doe@example.com",
//     "password": "s3CreT!@1a2B"
// }
```

### To Eloquent Model

```php
$dto = new UserDTO([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'password' => 's3CreT!@1a2B',
]);

$dto->toModel(\App\Models\User::class);
// App\Models\User {#3776
//     name: "John Doe",
//     email: "john.doe@example.com",
//     password: "s3CreT!@1a2B",
// }

```

## Customizing Error Messages, Attributes and Exceptions

You can define custom messages and attributes implementing the `messages` and `attributes` methods:

```php
/**
 * Defines the custom messages for validator errors.
 *
 * @return array
 */
public function messages(): array
{
    return [];
}

/**
 * Defines the custom attributes for validator errors.
 *
 * @return array
 */
public function attributes(): array
{
    return [];
}
```

You can define custom `Exceptions` implementing the `failedValidation` method:

```php
/**
 * Handles a failed validation attempt.
 *
 * @return void
 *
 * @throws ValidationException
 */
protected function failedValidation(): void
{
    throw new ValidationException($this->validator);
}
```

## Type Casting

You can easily cast your DTO properties by defining a casts method in your DTO:

```php
/**
 * Defines the type casting for the properties of the DTO.
 *
 * @return array
 */
protected function casts(): array
{
    return [
        'name' => new StringCast(),
        'age'  => new IntegerCast(),
        'created_at' => new CarbonImmutableCast(),
    ];
}
```

## Available Types

### Array

For JSON strings, it will convert into an array, for other types, it will wrap them in an array.

```php
protected function casts(): array
{
    return [
        'property' => new ArrayCast(),
    ];
}
```

### Boolean

For string values, this uses the `filter_var` function with the `FILTER_VALIDATE_BOOLEAN` flag.

```php
protected function casts(): array
{
    return [
        'property' => new BooleanCast(),
    ];
}
```

### Carbon

This accepts any value accepted by the `Carbon` constructor. If an invalid value is found it will throw a
`\WendellAdriel\ValidatedDTO\Exceptions\CastException` exception.

```php
protected function casts(): array
{
    return [
        'property' => new CarbonCast(),
    ];
}
```

You can also pass a timezone when defining the cast if you need that will be used when casting the value.

```php
protected function casts(): array
{
    return [
        'property' => new CarbonCast('Europe/Lisbon'),
    ];
}
```

You can also pass a format when defining the cast to be used to cast the value. If the property has a different format than
the specified it will throw a `\WendellAdriel\ValidatedDTO\Exceptions\CastException` exception.

```php
protected function casts(): array
{
    return [
        'property' => new CarbonCast('Europe/Lisbon', 'Y-m-d'),
    ];
}
```

### CarbonImmutable

This accepts any value accepted by the `CarbonImmutable` constructor. If an invalid value is found it will throw a
`\WendellAdriel\ValidatedDTO\Exceptions\CastException` exception.

```php
protected function casts(): array
{
    return [
        'property' => new CarbonImmutableCast(),
    ];
}
```

You can also pass a timezone when defining the cast if you need that will be used when casting the value.

```php
protected function casts(): array
{
    return [
        'property' => new CarbonImmutableCast('Europe/Lisbon'),
    ];
}
```

You can also pass a format when defining the cast to be used to cast the value. If the property has a different format than
the specified it will throw a `\WendellAdriel\ValidatedDTO\Exceptions\CastException` exception.

```php
protected function casts(): array
{
    return [
        'property' => new CarbonImmutableCast('Europe/Lisbon', 'Y-m-d'),
    ];
}
```

### Collection

For JSON strings, it will convert into an array first, before wrapping it into a `Collection` object.

```php
protected function casts(): array
{
    return [
        'property' => new CollectionCast(),
    ];
}
```

If you want to cast all the elements inside the `Collection`, you can pass a `Castable` to the `CollectionCast`
constructor. Let's say that you want to convert all the items inside the `Collection` into integers:

```php
protected function casts(): array
{
    return [
        'property' => new CollectionCast(new IntegerCast()),
    ];
}
```

This works with all `Castable`, including `DTOCast` and `ModelCast` for nested data.

### DTO

This works with arrays and JSON strings. This will validate the data and also cast the data for the given DTO.

This will throw a `Illuminate\Validation\ValidationException` exception if the data is not valid for the DTO.

This will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastException` exception if the property is not a valid
array or valid JSON string.

This will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastTargetException` exception if the class passed to the
`DTOCast` constructor is not a `ValidatedDTO` instance.

```php
protected function casts(): array
{
    return [
        'property' => new DTOCast(UserDTO::class),
    ];
}
```

### Float

If a not numeric value is found, it will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastException` exception.

```php
protected function casts(): array
{
    return [
        'property' => new FloatCast(),
    ];
}
```

### Integer

If a not numeric value is found, it will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastException` exception.

```php
protected function casts(): array
{
    return [
        'property' => new IntegerCast(),
    ];
}
```

### Model

This works with arrays and JSON strings.

This will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastException` exception if the property is not a valid
array or valid JSON string.

This will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastTargetException` exception if the class passed to the
`ModelCast` constructor is not a `Model` instance.

```php
protected function casts(): array
{
    return [
        'property' => new ModelCast(User::class),
    ];
}
```

### Object

This works with arrays and JSON strings.

This will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastException` exception if the property is not a valid
array or valid JSON string.

```php
protected function casts(): array
{
    return [
        'property' => new ObjectCast(),
    ];
}
```

### String

If the data can't be converted into a string, this will throw a `WendellAdriel\ValidatedDTO\Exceptions\CastException`
exception.

```php
protected function casts(): array
{
    return [
        'property' => new StringCast(),
    ];
}
```

## Create Your Own Type Cast

You can easily create new `Castable` types for your project by implementing the `WendellAdriel\ValidatedDTO\Casting\Castable`
interface. This interface has a single method that must be implemented:

```php
/**
 * Casts the given value.
 *
 * @param  string  $property
 * @param  mixed  $value
 * @return mixed
 */
public function cast(string $property, mixed $value): mixed;
```

Let's say that you have a `URLWrapper` class in your project, and you want that when passing a URL into your
`DTO` it will always return a `URLWrapper` instance instead of a simple string:

```php
class URLCast implements Castable
{
    /**
     * @param  string  $property
     * @param  mixed  $value
     * @return URLWrapper
     */
    public function cast(string $property, mixed $value): URLWrapper
    {
        return new URLWrapper($value);
    }
}
```

Then you could apply this to your DTO:

```php
class CustomDTO extends ValidatedDTO
{
    protected function rules(): array
    {
        return [
            'url' => ['required', 'url'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'url' => new URLCast(),
        ];
    }
}
```

## Casting Eloquent Model properties to DTOs

You can easily cast any **Eloquent Model** properties to your **DTOs**:

```php
class MyModel extends Model
{
    protected $fillable = ['name', 'metadata'];

    protected $casts = [
        'metadata' => AttributesDTO::class,
    ];
}
```

The **DTO** class:

```php
class AttributesDTO extends ValidatedDTO
{
    public int $age;

    public string $doc;

    protected function rules(): array
    {
        return [
            'age' => ['required', 'integer'],
            'doc' => ['required', 'string'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'age' => new IntegerCast(),
            'doc' => new StringCast(),
        ];
    }
}
```

## Credits

- [Wendell Adriel](https://github.com/WendellAdriel)
- [All Contributors](../../contributors)

## Contributing

Check the **[Contributing Guide](CONTRIBUTING.md)**.

<!---------------------------------------------------------------------------->
[Docs Button]: https://img.shields.io/badge/Documentation-40CA00?style=for-the-badge&logoColor=white&logo=GitBook
[Docs Link]: https://wendell-adriel.gitbook.io/laravel-validated-dto/
