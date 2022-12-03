# NBG Currency

[![Latest Stable Version](https://img.shields.io/packagist/v/Stichoza/nbg-currency.svg)](https://packagist.org/packages/stichoza/nbg-currency) [![Total Downloads](https://img.shields.io/packagist/dt/Stichoza/nbg-currency.svg)](https://packagist.org/packages/stichoza/nbg-currency)

National Bank of Georgia (NBG) currency service API wrapper in PHP

## Installation

Install this package via [Composer](https://getcomposer.org/).

```
composer require stichoza/nbg-currency
```
> Note: PHP 8.1 or later is required. Use following versions of this package for older PHP versions:

| Package version | PHP Version  | Documentation                                                                              |
|-----------------|--------------|--------------------------------------------------------------------------------------------|
| `v3.0`          | PHP >= 8.1   | [v3 Docs](#nbg-currency)                                                                   |
| `v2.0`          | PHP >= 7.1.8 | [v2 Docs](https://github.com/Stichoza/nbg-currency/tree/2.0#nbg-currency)                  |
| ~~`v1.2`~~      | PHP >= 5.5.9 | ~~[v1.2 Docs](https://github.com/Stichoza/nbg-currency/tree/1.2#nbg-currency)~~ (Outdated) |

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

| Parameter | Default | Description                        |
|-----------|---------|------------------------------------|
| `$code`   |         | Currency code, not case-sensitive) |
| `$date`   | `null`  | Date of currency rate              |

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

### Get Currency Object

The `NbgCurrency::get()` method returns a `Currency` object containing data of a currency for specified date.

This method accepts same parameters as `::rate()` method and one additional parameter for language (Used for currency name).

```php
NbgCurrency::get(string $code, DateTimeInterface|string|null $date = null, string $language = 'ka'): Currency
```

| Parameter   | Default | Description                       |
|-------------|---------|-----------------------------------|
| `$code`     |         | Currency code, not case-sensitive |
| `$date`     | `null`  | Date of currency rate             |
| `$language` | `ka`    | Language for currency name        |

**Examples:**

```php
$usd = NbgCurrency::get('usd'); // Currency object (Stichoza\NbgCurrency\Data\Currency)

$usd->code; // USD
$usd->rate; // 2.7112
$usd->name; // აშშ დოლარი
$usd->diff; // -0.0065
$usd->date; // Carbon object of date: 2022-12-01 17:45:12
$usd->validFrom // Carbon object since when the rate is valid: 2022-12-02 00:00:00
$usd->change; // Currency rate change. -1 if decreased, 0 if unchanged and 1 if increased.

// Using methods available on Carbon objects
$usd->date->format('j F Y'); // 1 December 2022
$usd->date->diffForHumans(); // 3 days ago
$usd->validFrom->isPast(); // true

// Additional methods
$usd->increased(); // Returns true if rate has increased, false otherwise.
$usd->decreased(); // Returns true if rate has decreased, false otherwise.
$usd->unchanged(); // Returns true if rate hasn't changed, false otherwise.

// Returns first parameter if rate was increased, second string if there was no change
// and third string if the rate went up. Useful for CSS classes, font icons, etc.
$class = $usd->changeString('text-red', 'text-gray', 'text-green');
$icon  = $usd->changeString('fa-arrow-down', 'fa-circle', 'fa-arrow-down');
```

> **Note:** When you 

## Advanced Usage





.

.

.

.

.

.

.

.

.

.

.

.

.
.
.
.
.
.
.



### Methods

##### `rate($currency)`

Get the currency rate.

```php
NbgCurrency::rate('usd'); // 2.3966
```

##### `diff($currency)`

Get the rate difference.

```php
NbgCurrency::diff('usd'); // 0.0017
```

##### `change($currency)`

Currency rate change. `-1` if decreased, `0` if unchanged and `1` if increased.

```php
NbgCurrency::change('usd'); // 1
```

##### `description($currency)`

Get the description of currency rate.

```php
NbgCurrency::description('eek'); // 10 ესტონური კრონი
```

##### `date()`

Get the date of currency rates. Returns [Carbon](http://carbon.nesbot.com) object representing the date. [All carbon methods](http://carbon.nesbot.com/docs/#api-difference) are available on this object.

```php
NbgCurrency::date();                  // 2016-01-01 00:00:00
NbgCurrency::date()->format('j F Y'); // 1 January 2016
NbgCurrency::date()->diffForHumans(); // 2 days ago
NbgCurrency::date()->isPast();        // true
// etc.
```

##### `get($currency)`

This method returns an object containing all data described above.

```php
$currency = NbgCurrency::get('usd');

$currency->date->format('j F Y'); // 1 January 2016
$currency->rate; // 2.3966
$currency->diff; // 0.0017
// etc.
```

##### `isSupported($currency)`

Check if the currency is supported.

```php
NbgCurrency::isSupported('usd'); // true
NbgCurrency::isSupported('lol'); // false
```

### Fluent Methods

Some methods (`get`, `rate`, `description`, `change`, `diff`) are available to call fluently like so:

```php
NbgCurrency::rateUsd(); // same as NbgCurrency::rate('usd');
NbgCurrency::ratePln(); // same as NbgCurrency::rate('pln');
NbgCurrency::diffEur(); // same as NbgCurrency::diff('eur');
NbgCurrency::getUah();  // same as NbgCurrency::get('uah');
// etc.
```

### General Recommendations

Each method call maps to a SOAP web service method. So it's better to remember a rate in a variable or even store a result in a cache if possible.

##### DO:
```php
$dollars = [199, 340.5, 230.25, 30, 78.99];
$usdRate = NbgCurrency::rateUsd();

foreach ($dollars as $d) {
	echo $d * $usdRate;
}
```

##### DO NOT:

```php
$dollars = [199, 340.5, 230.25, 30, 78.99];

foreach ($dollars as $d) {
	echo $d * NbgCurrency::rateUsd();
}
```

### Keywords

> ლარის კურსი, ეროვნული ბანკის გაცვლითი კურსი, ვალუტა, ლარის ვალუტის კურსი, laris kursi, laris valuta, lari currency, national bank of georgia, nbg
