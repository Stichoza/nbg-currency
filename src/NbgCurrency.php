<?php

namespace Stichoza\NbgCurrency;

use Carbon\Carbon;
use DateTimeInterface;
use Stichoza\NbgCurrency\Data\Currencies;
use Stichoza\NbgCurrency\Data\Currency;
use Throwable;

/**
 * NBG currency service wrapper class
 *
 * @author      Levan Velijanashvili <me@stichoza.com>
 * @link        https://stichoza.com/
 * @license     MIT
 */
class NbgCurrency
{
    public const TIMEZONE = 'Asia/Tbilisi';

    protected const URL = 'https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/ka/json';

    /**
     * @var \Stichoza\NbgCurrency\Data\Currencies[]
     */
    protected static array $currenciesByDate = [];

    public static function get(string $currency, DateTimeInterface|string|null $date = null): Currency
    {
        if ($date === null) {
            $carbon = Carbon::today(self::TIMEZONE);
        } else {
            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        }

        return self::date($carbon->toDateString())->get($currency);
    }

    public static function date(DateTimeInterface|string|null $date): Currencies
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        if (empty(self::$currenciesByDate[$carbon->toDateString()])) {

        }

        return self::$currenciesByDate[$carbon->toDateString()];
    }

    protected static function request(?Carbon $date = null): array
    {
        $query = $date ? ('?date=' . $date->toDateString()) : '';

        file_get_contents(self::URL . $query);
    }

}
