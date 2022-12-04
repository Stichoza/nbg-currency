# NBG Currency

[![Latest Stable Version](https://img.shields.io/packagist/v/Stichoza/nbg-currency.svg)](https://packagist.org/packages/stichoza/nbg-currency) [![Total Downloads](https://img.shields.io/packagist/dt/Stichoza/nbg-currency.svg)](https://packagist.org/packages/stichoza/nbg-currency)

National Bank of Georgia (NBG) currency service API wrapper in PHP

## Installation

Install this package via [Composer](https://getcomposer.org/).

```
composer require stichoza/nbg-currency
```
> Note: PHP 8.1 or later is required. Use following versions of this package for older PHP versions:

| Package version | PHP Version  | Documentation                                                                                 |
|-----------------|--------------|-----------------------------------------------------------------------------------------------|
| `v3.0`          | PHP >= 8.1   | [v3 Docs](#nbg-currency)                                                                      |
| `v2.0`          | PHP >= 7.1.8 | [v2 Docs](https://github.com/Stichoza/nbg-currency/tree/2.0#nbg-currency)                     |
| ~~`v1.2`~~      | PHP >= 5.5.9 | ~~[v1.2 Docs](https://github.com/Stichoza/nbg-currency/tree/1.2#nbg-currency)~~ (Not working) |

## Basic Usage

The class is namespaced as `Stichoza\NbgCurrency\NbgCurrency`:

```php
use Stichoza\NbgCurrency\NbgCurrency;
```

This package has three main static methods from which you can access currency rates.

### Get Currency Rate

The `NbgCurrency::rate()` method returns a currency rate in `float`.

**Note:** The rate is always for a **single unit**. The original NBG JSON API returns rate for different amounts per currency. For example Japanese Yen (JPY) rate will be 1.9865 and quantity will be set to 100 (100 JPY is 1.9865 GEL). It is quite confusing during calculations so **this package always returns price per single unit**. So in this case JPY will be 0.019865 (1 JPY is 0.019865 GEL).

```php
NbgCurrency::rate(string $code, DateTimeInterface|string|null $date = null): float
```

| Parameter | Default | Description                                                                                                                               |
|-----------|---------|-------------------------------------------------------------------------------------------------------------------------------------------|
| `$code`   |         | Currency code, not case-sensitive                                                                                                         |
| `$date`   | `null`  | Date of currency rate: [Carbon](https://carbon.nesbot.com), [DateTime](https://www.php.net/manual/en/class.datetime.php), string or null. |

**Examples:**

```php
NbgCurrency::rate('usd'); // Returns current rate of USD. Example: 2.7177
NbgCurrency::rate('usd', '2022-12-02'); // USD rate on 2022-12-02
NbgCurrency::rate('eur', 'yesterday'); // EUR rate from yesterday. Strings are parsed via Carbon::parse()
NbgCurrency::rate('eur', Carbon::yesterday()); // EUR rate from yesterday
NbgCurrency::rate('gbp', Carbon::today()->subDays(5)); // GBP rate 5 days ago
NbgCurrency::rate('gbp', new DateTime()); // GBP rate today

if (NbgCurrency::rate('usd') > 3) {
    echo 'Oh no!';
}
```

When passing dates as [`Carbon`](https://carbon.nesbot.com) or `DateTime` objects, it's recommended to have its timezone set to `Asia/Tbilisi` to avoid unexpected behavior. For convenience, timestamp string is available as `NbgCurrency::TIMEZONE` class constant.

### Get Currency Object

The `NbgCurrency::get()` method returns a `Currency` object containing data of a currency for specified date.

This method accepts same parameters as `::rate()` method and one additional parameter for language (Used for currency name).

```php
NbgCurrency::get(string $code, DateTimeInterface|string|null $date = null, string $language = 'ka'): Currency
```

| Parameter   | Default | Description                                              |
|-------------|---------|----------------------------------------------------------|
| `$code`     |         | Currency code, not case-sensitive                        |
| `$date`     | `null`  | Date of currency rate                                    |
| `$language` | `ka`    | Language for currency name (Currently only `en` or `ka`) |

**Examples:**

```php
$usd = NbgCurrency::get('usd'); // Currency object (Stichoza\NbgCurrency\Data\Currency)

$usd->code; // USD
$usd->rate; // 2.7112
$usd->name; // აშშ დოლარი
$usd->diff; // -0.0065
$usd->date; // Carbon object of date: 2022-12-01 17:45:12
$usd->validFrom; // Carbon object since when the rate is valid: 2022-12-02 00:00:00
$usd->change; // Currency rate change. -1 if decreased, 0 if unchanged and 1 if increased.

// Using methods available on Carbon objects
$usd->date->format('j F Y'); // 1 December 2022
$usd->date->diffForHumans(); // 3 days ago
$usd->validFrom->isPast(); // true

// Additional methods
$usd->increased(); // Returns true if rate has increased, false otherwise.
$usd->decreased(); // Returns true if rate has decreased, false otherwise.
$usd->unchanged(); // Returns true if rate hasn't changed, false otherwise.

// The changeString() method returns first parameter if rate was increased, second string if there was
// no change and third string if the rate went up. Useful for CSS classes, font icons, etc.
$class = $usd->changeString('text-red', 'text-gray', 'text-green');
$icon  = $usd->changeString('fa-arrow-down', 'fa-circle', 'fa-arrow-down');
```

**Note:** All properties of `Currency` class are declared as `readonly`. Updating them will result in Fatal Error.

## Advanced Usage

### Get All Currencies

The `NbgCurrency::date()` method will return a `Currencies` object. This is a collection-like object that contains a list of all `Currency` objects available for specified date.

```php
NbgCurrency::date(DateTimeInterface|string|null $date = null, string $language = 'ka'): Currencies
```

| Parameter   | Default | Description                                                    |
|-------------|---------|----------------------------------------------------------------|
| `$date`     | `null`  | Date of currency rates                                         |
| `$language` | `ka`    | Language for names of currencies (Currently only `en` or `ka`) |

**Examples:**

```php
$currencies = NbgCurrency::date('3 days ago');
$currencies = NbgCurrency::date(Carbon::now()->startOfMonth(), 'en');
```

`Currencies` class has date attribute and several methods that you can use.

```php
$currencies->date; // Carbon object of date

$currencies->get('usd'); // Returns Currency object for USD
$currencies->has('eur'); // True if EUR currency is contained in $currencies collection
$currencies->count(); // Count of Currency objects in collection

$currencies->get('usd')->rate; // Currency rate of USD
$currencies->get('eur')->date->diffForHumans(); // 10 days ago
```

Note that `->get()` method of `Currencies` object has only one parameter `string $code`, while the static method with the same name (`NbgCurrency::get()`) has two additional parameters described in basic usage examples above.

The `Currencies` object also implements `Countable` and `IteratorAggregate` interfaces, so you can use the object in `foreach` loops and `count()` function.

**Examples:**

```php
$currencies = NbgCurrency::date('2022-12-02', 'en'); // Currencies object (Stichoza\NbgCurrency\Data\Currencies) 

echo 'Total ' . count($currencies) . ' currencies for ' . $currencies->date->toFormattedDateString();
// Total 43 currencies for Dec 2, 2022

foreach ($currencies as $code => $currency) {
    echo $currency->code . ' costs ' . $currency->rate;
}
// AED costs 7.3662
// AMD costs 6.8453
// ...
```

## Error Handling

There are 5 exceptions in `Stichoza\NbgCurrency\Exceptions` namespace that could be thrown from methods:

 - `CurrencyNotFoundException` - If currency is not available.
 - `DateNotFoundException` - If specified date is not available.
 - `LanguageNotFoundException` - If specified language is not available.
 - `InvalidDateException` - If the specified date is in invalid format or cannot be parsed.
 - `RequestFailedException` - If there was an error during API request.

All exceptions above extend `Exception` class, so you can handle all exceptions by catching `Exception` or `Throwable`.

```php
try {
    $usdRate = NbgCurrency::date('10 days ago', 'en')->get('usd')->rate;
} catch (Exception) {
    // Whoops...
}
```

## Additional Info

### Number of HTTP Requests
When you access any currency, all currencies for that day in selected language will be fetched (API returns all currencies in a single request) and stored in NbgCurrency class attribute. When you request any other currency, no additional HTTP requests will be made for same date and language.

```php
// Next 3 method calls will result in 1 HTTP request in total.
NbgCurrency::rate('usd');
NbgCurrency::rate('eur');
NbgCurrency::rate('gbp');

// Next 3 method calls will result in 3 HTTP requests in total.
NbgGurrency::rate('usd', '2022-10-10');
NbgGurrency::rate('eur', '2022-11-11', 'en');
NbgGurrency::rate('gbp', '2022-11-11');

// Next 3 method calls will result in 2 HTTP request in total.
NbgGurrency::rate('usd', '2022-11-11');
NbgGurrency::rate('eur', '2022-11-11');
NbgGurrency::rate('gbp', '2022-11-11', 'en');
```

### Memory Usage & Caching

By default, all retrieved currencies are stored in a static property of `NbgCurrency` class. If you're planning to get currencies for many different dates, it might use excessive memory. In this case it's recommended to turn off the caching feature.

```php
NbgCurrency::disableCaching(); // Disable caching, also all data stored in the property.
NbgCurrency::enableCaching(); // Enables caching in class property.
```

On the other hand, disabling caching may increase number of HTTP requests being sent. Example:

```php
$codes = ['USD', 'EUR', 'GBP', 'UAH', 'JPY'];

foreach ($codes as $code) {
    echo NbgCurrency::rate($code);
}
```

The above code would make a single HTTP request to the API when caching is enabled. But if you disable caching, it will send 5 separate HTTP requests. To load multiple currencies of same date using a single HTTP request with caching disabled, you can use `::date()` method to get `Currencies` object and then access all contained objects after.

```php
$codes = ['USD', 'EUR', 'GBP', 'UAH', 'JPY'];

$currencies = NbgCurrency::date();

foreach ($codes as $code) {
    echo $currencies->get($code)->rate;
}
```

In this case there will be a single HTTP request made even with caching disabled.

### Keywords

> ლარის კურსი, ეროვნული ბანკის გაცვლითი კურსი, ვალუტა, ლარის ვალუტის კურსი, laris kursi, laris valuta, lari currency, national bank of georgia, nbg
