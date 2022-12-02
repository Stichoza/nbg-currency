<?php
namespace Stichoza\NbgCurrency\Tests;

use PHPUnit\Framework\TestCase;
use Stichoza\NbgCurrency\Data\Currencies;
use Stichoza\NbgCurrency\Data\Currency;
use Stichoza\NbgCurrency\NbgCurrency;

class InterfacesTest extends TestCase
{
    public Currencies $currencies;

    protected function setUp(): void
    {
        $this->currencies = NbgCurrency::date();
    }

    public function testIterator(): void
    {
        foreach ($this->currencies as $code => $currency) {
            $this->assertInstanceOf(Currency::class, $currency);
            $this->assertIsString($code);
            $this->assertIsNumeric($currency->rate);
        }
    }

    public function testCountable(): void
    {
        $count = count($this->currencies);

        $this->assertGreaterThan(0, $count);
        $this->assertEquals($count, $this->currencies->count());
    }
}
