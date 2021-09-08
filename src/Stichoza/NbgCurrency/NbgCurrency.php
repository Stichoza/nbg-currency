<?php

namespace Stichoza\NbgCurrency;

use BadMethodCallException;
use Carbon\Carbon;
use Exception;
use stdClass;
use Throwable;

/**
 * NBG currency service wrapper class
 *
 * @author      Levan Velijanashvili <me@stichoza.com>
 * @link        http://stichoza.com/
 * @license     MIT
 */
class NbgCurrency
{
    protected static $data = null;

    /**
     * @var string JSON URL
     */
    protected static $url = 'https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/ka/json';

    /**
     * @var array List of fluent methods
     */
    protected static $fluentMethods = ['get', 'change', 'diff', 'rate', 'description', 'name'];

    /**
     * Make sure the data is fetched.
     *
     * @throws \InvalidArgumentException
     */
    protected static function fetch(bool $force = false): void
    {
        if (self::$data && !$force) {
            return;
        }

        try {
            $data = file_get_contents(self::$url); // Get URL contents
            $data = json_decode($data, true); // Decode JSON to associative array
            $data = $data[0]['currencies']; // Get currency data
            $data = array_combine(array_column($data, 'code'), $data); // Set code as array keys

            self::$data = $data;
        } catch (Throwable $e) {
            throw new \ErrorException('Error fetching data from NBG');
        }
    }

    /**
     * Transform string to valid currency string.
     *
     * @param string $currency Input string
     *
     * @throws \InvalidArgumentException
     */
    protected static function checkCurrency(string $currency): void
    {
        if (!self::isSupported($currency)) {
            throw new \InvalidArgumentException('Currency ' . $currency . 'is not supported.');
        }
    }

    /**
     * Transform string to valid currency string.
     *
     * @param  string $currency Input string
     * @return string Output string
     */
    protected static function format(string $currency): string
    {
        return strtoupper($currency);
    }

    /**
     * Check if currency is supported.
     *
     * @param string $currency Currency
     *
     * @return bool If the currency is supported
     * @throws \InvalidArgumentException
     */
    public static function isSupported(string $currency): bool
    {
        self::fetch();

        return isset(self::$data[self::format($currency)]);
    }

    /**
     * @throws \InvalidArgumentException
     */
    public static function refresh()
    {
        self::fetch(true);
    }

    /**
     * Get the date of exchange rates.
     *
     * @param string $currency Currency
     *
     * @return Carbon A Carbon object representing the date
     * @throws \InvalidArgumentException
     */
    public static function date(string $currency = 'USD'): Carbon
    {
        self::fetch();

        return Carbon::parse(self::$data[self::format($currency)]['date']);
    }

    /**
     * Get the currency rate.
     *
     * @param string $currency Currency
     *
     * @return float Currency rate
     * @throws \InvalidArgumentException
     */
    public static function rate(string $currency = 'USD'): float
    {
        self::fetch();

        return (float) self::$data[self::format($currency)]['rate'];
    }

    /**
     * Get the currency rate name.
     *
     * @param string $currency Currency
     *
     * @return string Currency rate name
     * @throws \InvalidArgumentException
     */
    public static function name(string $currency): ?string
    {
        self::fetch();

        return self::$data[self::format($currency)]['name'];
    }

    /**
     * Get the currency rate description. Alias of name.
     *
     * @param string $currency Currency
     *
     * @deprecated Use name() instead.
     *
     * @return string Currency rate description
     * @throws \InvalidArgumentException
     */
    public static function description(string $currency): ?string
    {
        return self::name($currency);
    }

    /**
     * Get the currency rate difference.
     *
     * @param string $currency Currency
     * @param bool $absolute Return absolute value. This is for legacy reasons. Will be removed.
     *
     * @return float Currency rate difference
     * @throws \InvalidArgumentException
     */
    public static function diff(string $currency, bool $absolute = true): float
    {
        self::fetch();

        $diff = self::$data[self::format($currency)]['diff'];

        return (float) $absolute ? abs($diff) : $diff;
    }

    /**
     * Get the currency rate change status (-1 if decreased, 0 is unchanged, 1 if increased).
     *
     * @param string $currency Currency
     *
     * @return int Currency rate change
     * @throws \InvalidArgumentException
     */
    public static function change(string $currency): int
    {
        self::fetch();

        return self::diff($currency, false) <=> 0;
    }

    /**
     * Get all information about currency rate.
     *
     * @param string $currency Currency
     *
     * @return object Currency rate data
     * @throws \InvalidArgumentException
     */
    public static function get(string $currency): stdClass
    {
        return (object) [
            'date'        => self::date(),
            'rate'        => self::rate($currency),
            'diff'        => self::diff($currency),
            'description' => self::description($currency),
            'change'      => self::change($currency),
        ];
    }

    /**
     * Handle fluent method calls.
     *
     * @param  string $name Method name
     * @param  string $args Method arguments
     * @return mixed Result
     */
    public static function __callStatic($name, $args)
    {
        foreach (self::$fluentMethods as $method) {
            if (preg_match('/^' . $method . '/', $name)) {
                return self::$method(substr($name, strlen($method))
                );
            }
        }
        throw new BadMethodCallException('Method ' . $name . ' does not exist');
    }

}
