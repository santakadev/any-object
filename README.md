Any Object
=====================

A PHP 8.1+ library to generate random objects of any class.

## Installation

```bash
composer require --dev santakadev/any-object
```

## Usage

Generate a random object:

```php
$any = new AnyObject();
$object = $any->of(Product::class);
```

Fix some properties and use a random value for the rest

```php
$any = new AnyObject();
$object = $any->of(class: Product::class, with: ['name' => 'My Product']);
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
