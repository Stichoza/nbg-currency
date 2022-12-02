<?php
namespace Stichoza\NbgCurrency\Tests;

use PHPUnit\Framework\TestCase;
use Stichoza\NbgCurrency\Data\Currency;
use Stichoza\NbgCurrency\NbgCurrency;

class IteratorTest extends TestCase
{
    public function testIterator(): void
    {
        $currencies = NbgCurrency::date();

        foreach ($currencies as $code => $currency) {
            $this->assertInstanceOf(Currency::class, $currency);
            $this->assertIsString($code);
            $this->assertIsNumeric($currency->rate);
        }
    }
}
