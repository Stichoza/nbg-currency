<?php

namespace Stichoza\NbgCurrency\Data;

use Carbon\Carbon;
use Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException;
use Stichoza\NbgCurrency\NbgCurrency;
use Throwable;

class Currencies
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
     * @throws \Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException
     */
    public function get(string $code): Currency
    {
        return $this->currencies[strtolower($code)] ?? throw new CurrencyNotFoundException;
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

    protected function pushCurrencies(array $data): void
    {
        array_map($this->pushCurrency(...), $data);
    }
}