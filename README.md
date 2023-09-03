Any Object
=====================

A PHP 8.1+ library to generate random objects of any class.

## Installation

```bash
composer require --dev santakadev/any-object
```

## Usage

```php
$any = new AnyObject();
$object = $any->of(Product::class);
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
