# NBG Currency
National Bank of Georgia (NBG) currency service API wrapper in PHP


## Installation

Install this package via [Composer](https://getcomposer.org/).

```
composer require stichoza/nbg-currency
```

## Usage

This package is very easy to use and has only 6 methods. **Keep in mind that method names are not same as NBG's SOAP service.** This package has more intuitive method names.

In addition, currencies are **not** case-sensitive here.

### Methods

#### `rate($currency)`

Get the currency rate.

```php
NbgCurrency::rate('usd'); // 2.3966
```

#### `diff($currency)`

Get the rate difference.

```php
NbgCurrency::diff('usd'); // 0.0017
```

#### `change($currency)`

Currency rate change. `-1` if decreased, `0` if unchanged and `1` if increased.

```php
NbgCurrency::change('usd'); // 1
```

#### `text($currency)`

Get the description of currency rate.

```php
NbgCurrency::text('eek'); // 10 ესტონური კრონი
```

#### `date()`

Get the date of currency rates. Returns [Carbon](http://carbon.nesbot.com) object representing the date. [All carbon methods](http://carbon.nesbot.com/docs/#api-difference) are available on this object.

```php
NbgCurrency::date();                  // 2016-01-01 00:00:00
NbgCurrency::date()->format('j F Y'); // 1 January 2016
NbgCurrency::date()->diffForHumans(); // 2 days ago
NbgCurrency::date()->isPast();        // true
// etc.
```

#### `get()`

This method returns an object containing all data described above.

```php
$currency = NbgCurrency::get('usd');
$currency->date->format('j F Y');
$currency->rate;
$currency->diff;
// etc.
```

#### `currencyIsSupported($currency)`

Check if the currency is supported.

```php
NbgCurrency::currencyIsSupported('usd'); // true
NbgCurrency::currencyIsSupported('lol'); // false
```

### Fluent Methods

Some methods (`get`, `rate`, `text`, `change`, `diff`) are available to call fluently like so:

```php
NbgCurrency::rateUsd(); // same as NbgCurrency::rate('usd');
NbgCurrency::ratePln(); // same as NbgCurrency::rate('pln');
NbgCurrency::diffEur(); // same as NbgCurrency::diff('eur');
NbgCurrency::getUah();  // same as NbgCurrency::get('uah');
// etc.
```
