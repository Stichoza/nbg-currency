<?php

namespace Stichoza\NbgCurrency\Tests;

use PHPUnit\Framework\TestCase;
use Stichoza\NbgCurrency\Exceptions\CurrencyNotFoundException;
use Stichoza\NbgCurrency\Exceptions\DateNotFoundException;
use Stichoza\NbgCurrency\Exceptions\InvalidDateException;
use Stichoza\NbgCurrency\Exceptions\LanguageNotAllowedException;
use Stichoza\NbgCurrency\Exceptions\RequestFailedException;
use Stichoza\NbgCurrency\NbgCurrency;

class ExceptionsTest extends TestCase
{
    public function testCurrencyNotFoundException(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        NbgCurrency::get('abc', throwExceptions: true);
    }

    public function testCurrencyNotFoundExceptionWithDate(): void
    {
        $this->expectException(CurrencyNotFoundException::class);

        NbgCurrency::date('yesterday', throwExceptions: true)->get('abc');
    }

    public function testDateNotFoundException(): void
    {
        $this->expectException(DateNotFoundException::class);

        NbgCurrency::date('2000-01-01', throwExceptions: true);
    }

    public function testInvalidDateException(): void
    {
        $this->expectException(InvalidDateException::class);

        NbgCurrency::date('123', throwExceptions: true);
    }

    public function testInvalidDateExceptionFuture(): void
    {
        $this->expectException(InvalidDateException::class);

        NbgCurrency::date('in 5 days', throwExceptions: true);
    }

    /*
     * Incorrect language results in 422 Unprocessable Entity
     */
    public function testRequestFailedException(): void
    {
        $this->expectException(RequestFailedException::class);

        NbgCurrency::date(language: 'ab', throwExceptions: true);
    }
}
