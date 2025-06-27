# tr-address

A Laravel package for Turkey's provinces, districts, neighborhoods, subdistricts, and postal codes. Easily import, query, and keep up-to-date address data from PTT's official source.

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

## Data Structure

Neighborhoods now support a `subdistrict` (quarter) field. If your JSON data contains entries like:

```json
{
  "name": "BEYAZEVLER MAH / MAHFESIĞMAZ / 01170"
}
```

- `name` → "BEYAZEVLER MAH"
- `subdistrict` → "MAHFESIĞMAZ"
- `postcode` → "01170"

The seeder will automatically parse and store these fields in the correct columns.

## Seeding the Database

After publishing migrations and running `php artisan migrate`, you can seed the address data:

```bash
php artisan db:seed --class=Database\Seeders\TrAddressSeeder
```

Or, using the package's import command:

```bash
php artisan traddress:import tr-address-data.json
```

You can also seed only a specific level of data:

```bash
php artisan db:seed --class=Database\Seeders\CitySeeder
php artisan db:seed --class=Database\Seeders\DistrictSeeder
php artisan db:seed --class=Database\Seeders\NeighborhoodSeeder
php artisan db:seed --class=Database\Seeders\PostcodeSeeder
```

The main seeder (`TrAddressSeeder`) will run all of these in order.

## Usage

```php
use TrAddress\Models\City;

$cities = City::all();

// Access subdistrict (quarter) from a neighborhood
$neighborhood = \TrAddress\Models\Neighborhood::first();
echo $neighborhood->subdistrict;
```

## License
MIT 