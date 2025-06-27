# tr-address

A Laravel package for Turkey's provinces, districts, neighborhoods, and postal codes. Easily import, query, and keep up-to-date address data from PTT's official source.

## Installation

```bash
composer require madkhix/tr-address
```

## Publishing Migrations, Seeders, and Config

```bash
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="migrations"
php artisan migrate
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="seeders"
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="traddress-config"
```

> After running the Python scraper, copy the generated `tr-address-data.json` file to your Laravel project root (where the `artisan` file is located). You can also use the following artisan command to copy it automatically:
>
> ```bash
> php artisan traddress:publish-json
> ```

> You can change the JSON data file path in `config/traddress.php` if needed.

## Usage

```php
use TrAddress\Models\City;

$cities = City::all();
```

## License
MIT 