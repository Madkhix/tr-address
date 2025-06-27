# tr-address

Türkiye il, ilçe, mahalle/semt ve posta kodu veritabanı ve Laravel paketi.

## Kurulum

```bash
composer require tr-address/tr-address
```

## Migration ve Seed Yayınlama

```bash
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="TrAddress\TrAddressServiceProvider" --tag="seeders"
```

## Kullanım

```php
use TrAddress\Models\City;

$cities = City::all();
```

## Lisans
MIT 