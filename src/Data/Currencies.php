<?php

namespace Stichoza\NbgCurrency\Data;

use ArrayIterator;
use Carbon\Carbon;
use Countable;
use Exception;
use IteratorAggregate;
use Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException;
use Stichoza\NbgCurrency\NbgCurrency;
use Throwable;
use Traversable;

class Currencies implements IteratorAggregate, Countable
{
    public readonly Carbon $date;

    /**
     * @var array<string, \Stichoza\NbgCurrency\Data\Currency> Array of currencies
     */
    protected array $currencies = [];

    public function __construct(array $data, Carbon $date)
    {
        $this->date = $date;
        $this->pushCurrencies($data);
    }

    /**
     * Get currency object
     *
     * @param string $code Currency code
     *
     * @return \Stichoza\NbgCurrency\Data\Currency
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     */
    public function get(string $code): Currency
    {
        return $this->currencies[strtolower($code)] ?? throw new CurrencyNotFoundException;
    }

    /**
     * Check if a given currency is contained in currencies array
     *
     * @param string $code Currency code
     *
     * @return bool If a given currency is contained in currencies array
     */
    public function has(string $code): bool
    {
        return (bool) ($this->currencies[strtolower($code)] ?? false);
    }

    /**
     * Push new currency data
     *
     * @param array $data Array from API
     *
     * @return \Stichoza\NbgCurrency\Data\Currency|null
     */
    protected function pushCurrency(array $data): ?Currency
    {
        if ($data['code'] ?? false) {
            $code = strtolower($data['code']);
        } else {
            return null;
        }

        try {
            $currency = new Currency(
                code: $data['code'],
                rate: $data['rate'] / ($data['quantity'] ?: 1),
                name: $data['name'],
                diff: $data['diff'] / ($data['quantity'] ?: 1),
                date: Carbon::parse($data['date'], NbgCurrency::TIMEZONE),
                validFrom: Carbon::parse($data['validFromDate'], NbgCurrency::TIMEZONE),
            );
        } catch (Throwable) {
            return null;
        }

        return $this->currencies[$code] = $currency;
    }

    /**
     * Push multiple items from raw array to $currencies
     *
     * @param array $data Raw data array
     *
     * @return void
     */
    protected function pushCurrencies(array $data): void
    {
        array_map($this->pushCurrency(...), $data);
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable<string, \Stichoza\NbgCurrency\Data\Currency>|\Stichoza\NbgCurrency\Data\Currency[]
     * @throws Exception on failure.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->currencies);
    }

    /**
     * Count of currencies
     *
     * @return int<0,max> The count of currencies
     */
    public function count(): int
    {
        return count($this->currencies);
    }
}
