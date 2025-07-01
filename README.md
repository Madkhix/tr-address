# tr-address

A Laravel package for Turkey's provinces, districts, quarters (subdistricts), neighborhoods, and postal codes. Easily import, query, and keep up-to-date address data from https://www.postakodu.web.tr/ official source.

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
>
> You can change the JSON data file path in `config/traddress.php` if needed.

## Data Structure

The JSON data must have the following structure:

```json
[
  {
    "name": "ADANA",
    "districts": [
      {
        "name": "ALADAĞ",
        "quarters": [
          {
            "name": "KARSANTI",
            "neighborhoods": [
              { "name": "MANSURLU MAH.", "postcode": "01720" }
            ]
          }
        ]
      }
    ]
  }
]
```
- `cities` have many `districts`
- `districts` have many `quarters` (subdistricts)
- `quarters` have many `neighborhoods`
- Each `neighborhood` has a `postcode`

## Migration Structure

Each table has its own migration file:
- `cities`
- `districts`
- `subdistricts` (quarters)
- `neighborhoods`
- `postcodes`

You can run all migrations at once:

```bash
php artisan migrate
```

Or, to run a specific migration file (advanced usage):

```bash
php artisan migrate --path=src/database/migrations/2024_01_03_000000_create_subdistricts_table.php
```

## Seeder Structure

Each table has its own seeder:
- `CitySeeder`
- `DistrictSeeder`
- `SubdistrictSeeder` (seeds all quarters)
- `NeighborhoodSeeder` (seeds all neighborhoods and links to subdistricts)
- `PostcodeSeeder`
- `TrAddressSeeder` (runs all in order)

Seed all data:

```bash
php artisan db:seed --class=Database\Seeders\TrAddressSeeder
```

> **Note:**
> - Run `SubdistrictSeeder` before `NeighborhoodSeeder` if you seed them separately.
> - `NeighborhoodSeeder` will not create subdistricts, only link to existing ones.

## Importing Data

After generating or updating your `tr-address-data.json` file, import it with:

```bash
php artisan traddress:import tr-address-data.json
```

## Usage

```php
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Subdistrict;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Postcode;

$cities = City::all();
$districts = $cities[0]->districts;
$quarters = $districts[0]->subdistricts; // quarters
$neighborhoods = $quarters[0]->neighborhoods;
$postcode = $neighborhoods[0]->postcodes->first()->code;
```

## Updating Address Data (Fetching from PTT)

This package does **not** include the Python data fetcher script by default. To update the address data from the official PTT source, use the separate Python script available at:

https://github.com/madkhix/tr-address-fetcher

**Usage:**

1. Clone or download the script from the repository above.
2. Run the script in your terminal:
   ```bash
   python fetch_tr_address_data.py
   ```
3. The script will generate a `tr-address-data.json` file.
4. Copy this file to your Laravel project root (where `artisan` is located).
5. Import the data using the package's artisan command:
   ```bash
   php artisan traddress:import tr-address-data.json
   ```

> This approach keeps the PHP package clean and dependency-free, while still allowing advanced users to update the address data as needed.

## License
MIT 