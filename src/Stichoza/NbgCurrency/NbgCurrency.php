<?php

namespace Stichoza\NbgCurrency;

use BadMethodCallException;
use Carbon\Carbon;
use SoapClient;

/**
 * NBG currency service wrapper class
 *
 * @author      Levan Velijanashvili <me@stichoza.com>
 * @link        http://stichoza.com/
 * @license     MIT
 */
class NbgCurrency
{
    /**
     * @var SoapClient
     */
    private static $client;

    /**
     * @var string WSDL address
     */
    private static $wsdl = 'http://nbg.gov.ge/currency.wsdl';

    /**
     * @var array List of all supported currencies
     */
    private static $supportedCurrencies = [
        'AED', 'AMD', 'AUD', 'AZN', 'BGN', 'BYR', 'CAD', 'CHF', 'CNY', 'CZK', 'DKK',
        'EEK', 'EGP', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'INR', 'IRR', 'ISK', 'JPY',
        'KGS', 'KWD', 'KZT', 'LTL', 'LVL', 'MDL', 'NOK', 'NZD', 'PLN', 'RON', 'RSD',
        'RUB', 'SEK', 'SGD', 'TJS', 'TMT', 'TRY', 'UAH', 'USD', 'UZS',
    ];
    
    /**
     * @var Array List of fluent methods
     */
    private static $fluentMethods = ['change', 'diff', 'rate', 'text'];

    /**
     * Check is SOAP client is set and instantiate if not.
     */
    private static function checkClient() {
        if ( ! isset(self::$client)) {
            self::$client = new SoapClient(self::$wsdl);
        }
    }

    /**
     * Transform string to valid currency string
     * @param  string $currency Input string
     * @return string Output string
     */
    private static function transformToValidCurrency($currency) {
        return strtoupper($currency);
    }

    /**
     * Check if currency is supported.
     * @param  string $currency Currency
     * @return boolean If the currency is supported
     */
    public static function currencyIsSupported($currency)
    {
        return in_array(strtoupper($currency), self::$supportedCurrencies);
    }

    /**
     * Get the date of exchange rates
     * @return Carbon\Carbon A Carbon object representing the date
     */
    public static function date()
    {
        self::checkClient();
        return Carbon::parse(self::$client->GetDate());
    }

    /**
     * Get the currency rate
     * @param  string $currency Currency
     * @return double Currency rate
     */
    public static function rate($currency)
    {
        self::checkClient();
        return (double) self::$client->GetCurrency(self::transformToValidCurrency($currency));
    }

    /**
     * Get the currency rate description
     * @param  string $currency Currency
     * @return string Currency rate description
     */
    public static function text($currency)
    {
        self::checkClient();
        return self::$client->GetDescription(self::transformToValidCurrency($currency));
    }

    /**
     * Get the currency rate difference
     * @param  string $currency Currency
     * @return double Currency rate difference
     */
    public static function diff($currency)
    {
        self::checkClient();
        return (double) self::$client->GetCurrencyChange(self::transformToValidCurrency($currency));
    }

    /**
     * Get the currency rate change status (-1 if decreased, 0 is unchanged, 1 if increased)
     * @param  string $currency Currency
     * @return int Currency rate change
     */
    public static function change($currency)
    {
        self::checkClient();
        return (int) self::$client->GetCurrencyRate(self::transformToValidCurrency($currency));
    }

    /**
     * Handle fluent method calls
     * @param  string $name Method name
     * @param  string $args Method arguments
     * @return mixed Result
     */
    public static function __callStatic($name, $args)
    {
        foreach (self::$fluentMethods as $method) {
            if (preg_match('/^' . $method . '/', $name)) {
                return self::$method(substr($name, strlen($method)));
            }
        }
        throw new BadMethodCallException("Method [{$name}] does not exist");
    }

}
