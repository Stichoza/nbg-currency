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
| ~~`v1.2`~~      | PHP >= 5.5.9 | ~~[v1.2 Docs](https://github.com/Stichoza/nbg-currency/tree/1.2#nbg-currency)~~ (not working) |

## Usage

The class is namespaced as `Stichoza\NbgCurrency\NbgCurrency`:

```php
use Stichoza\NbgCurrency\NbgCurrency;
```

This package is very easy to use and has a few methods.

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
