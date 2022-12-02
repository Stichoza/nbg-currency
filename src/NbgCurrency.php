<?php

namespace Stichoza\NbgCurrency;

use Carbon\Carbon;
use DateTimeInterface;
use JsonException;
use Stichoza\NbgCurrency\Data\Currencies;
use Stichoza\NbgCurrency\Data\Currency;
use Stichoza\NbgCurrency\Exceptions\DateNotFoundException;
use Stichoza\NbgCurrency\Exceptions\LanguageNotAllowedException;
use Stichoza\NbgCurrency\Exceptions\RequestFailedException;
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

    protected const URL = 'https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/%s/json';

    /**
     * @var \Stichoza\NbgCurrency\Data\Currencies[]
     */
    protected static array $currenciesByDate = [];

    public static function get(string $currency, DateTimeInterface|string|null $date = null, string $language = 'ka'): Currency
    {
        if ($date === null) {
            $carbon = Carbon::today(self::TIMEZONE);
        } else {
            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        }

        return self::date($carbon->toDateString(), $language)->get($currency);
    }

    public static function date(DateTimeInterface|string|null $date, string $language = 'ka'): Currencies
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        if (empty(self::$currenciesByDate[$carbon->toDateString()])) {
            self::$currenciesByDate[$carbon->toDateString()] = self::request($date, $language);
        }

        return self::$currenciesByDate[$carbon->toDateString()];
    }

    /**
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotAllowedException
     * @throws \JsonException
     */
    protected static function request(?Carbon $date = null, string $language = 'ka'): Currencies
    {
        $query = $date ? ('?date=' . $date->toDateString()) : '';

        $json = file_get_contents(sprintf(self::URL, $language) . $query);

        if ($json === false) {
            throw new RequestFailedException;
        }

        $array = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        // Seriously, it's `langaugeCode` in the API, lol
        if (($array['errors']['key'] ?? null) === 'langaugeCode') {
            throw new LanguageNotAllowedException;
        }

        if (!$array || empty($array[0]['date']) || empty($array[0]['currencies'])) {
            throw new DateNotFoundException;
        }

        return new Currencies(
            data: $array[0]['currencies'],
            date: Carbon::parse($array[0]['date'], self::TIMEZONE)
        );
    }

}
