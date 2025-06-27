# tr-address

A Laravel package for Turkey's provinces, districts, neighborhoods, and postal codes. Easily import, query, and keep up-to-date address data from PTT's official source.

## Installation

```bash
composer require madkhix/tr-address
```

## Publishing Migrations and Seeders

```bash
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="seeders"
```

## Usage

```php
use TrAddress\Models\City;

$cities = City::all();
```

## License
MIT 