Any Object
=====================
![packagist](https://img.shields.io/packagist/v/santakadev/any-object.svg?style=flat-square)
![license](https://img.shields.io/github/license/santakadev/any-object)
![build](https://github.com/santakadev/any-object/actions/workflows/ci.yaml/badge.svg)

> [!WARNING]  
> This library is under active development and will continue to evolve until version 1.0. Features and API may change. Use in production at your own risk.

**Any Object** generates random instances of any class with zero configuration.
Objects can be generated on-the-fly or with the codegen feature, which allows you
to create and maintain Object Mothers and Object Builders automatically.

## Features
- **Zero Configuration:** Any Object attempts to create objects with minimal setup.
- **Flexible Object Creation:** Supports setting specific properties while randomizing the rest.
- **Code Generation:** Automatically builds and maintains Object Mothers and Builders for test purposes.

## Installation

Install Any Object via [Composer](https://getcomposer.org/) as a development dependency:

```bash
composer require --dev santakadev/any-object
```

## Usage

### Generate a random object

You can quickly generate a random object of a given class:

```php
$any = new AnyObject();
$object = $any->of(Product::class);
```

### Customize properties

Fix certain properties while randomizing the rest:

```php
$any = new AnyObject();
$object = $any->of(Product::class, with: ['name' => 'My Product']);
```

## License

This library is open-sourced under the [License File](LICENSE).
