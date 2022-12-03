<?php

namespace Stichoza\NbgCurrency;

use Carbon\Carbon;
use DateTimeInterface;
use Stichoza\NbgCurrency\Data\Currencies;
use Stichoza\NbgCurrency\Data\Currency;
use Stichoza\NbgCurrency\Exceptions\DateNotFoundException;
use Stichoza\NbgCurrency\Exceptions\InvalidDateException;
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
     * @var array<string, array<string, Currencies>>
     */
    protected static array $currencies = [];

    /**
     * @param string $currency Currency code (USD, EUR, etc.); Case-insensitive
     * @param \DateTimeInterface|string|null $date Date of currency rates
     * @param string $language Language of currency names
     *
     * @return \Stichoza\NbgCurrency\Data\Currency Currency object
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\InvalidDateException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotAllowedException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    public static function get(string $currency, DateTimeInterface|string|null $date = null, string $language = 'ka'): Currency
    {
        return self::date($date, $language)?->get($currency);
    }

    /**
     * @param \DateTimeInterface|string|null $date Date of currency rates
     * @param string $language Language of currency names
     *
     * @return \Stichoza\NbgCurrency\Data\Currencies Collection of Currency objects
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\InvalidDateException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotAllowedException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    public static function date(DateTimeInterface|string|null $date = null, string $language = 'ka'): Currencies
    {
        if ($date !== null) {
            try {
                $carbon = $date instanceof Carbon ? $date : Carbon::parse($date, self::TIMEZONE);
            } catch (Throwable $e) {
                throw new InvalidDateException($e->getMessage());
            }
        } else {
            $carbon = Carbon::today(self::TIMEZONE);
        }

        return self::$currencies[$language][$carbon->toDateString()] ??= self::request($carbon, $language);
    }

    /**
     * @param \Carbon\Carbon|null $date Date of currency rates
     * @param string $language Language of currency names
     *
     * @return \Stichoza\NbgCurrency\Data\Currencies Collection of Currency objects
     * @throws \Stichoza\NbgCurrency\Exceptions\DateNotFoundException
     * @throws \Stichoza\NbgCurrency\Exceptions\LanguageNotAllowedException
     * @throws \Stichoza\NbgCurrency\Exceptions\RequestFailedException
     */
    protected static function request(?Carbon $date = null, string $language = 'ka'): Currencies
    {
        $query = $date ? ('?date=' . $date->toDateString()) : '';

        if ($date->isFuture()) {
            throw new DateNotFoundException('Date should not be in the future');
        }

        try {
            $json = file_get_contents(sprintf(self::URL, $language) . $query);
        } catch (Throwable $e) {
            throw new RequestFailedException($e->getMessage());
        }

        if ($json === false) {
            throw new RequestFailedException('Got empty response');
        }

        try {
            $array = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            throw new DateNotFoundException('Error decoding JSON');
        }

        // Seriously, it's `langaugeCode` in the API, lol
        if (($array['errors']['key'] ?? null) === 'langaugeCode') {
            throw new LanguageNotAllowedException('Language code "' . $language . '" is not allowed');
        }

        if (!$array || empty($array[0]['date']) || empty($array[0]['currencies'])) {
            throw new DateNotFoundException('No rates found for ' . $date->toDateString());
        }

        return new Currencies(
            data: $array[0]['currencies'],
            date: $date
        );
    }

}
