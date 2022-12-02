<?php
namespace Stichoza\NbgCurrency\Tests;

use Carbon\Carbon;
use DateTime;
use PHPUnit\Framework\TestCase;
use Stichoza\NbgCurrency\Data\Currencies;
use Stichoza\NbgCurrency\NbgCurrency;

class CurrencyTest extends TestCase
{
    public function testStaticGet(): void
    {
        $currency = NbgCurrency::get('usd');

        $this->assertEqualsIgnoringCase('usd', $currency->code);
        $this->assertGreaterThan(0, $currency->rate);
        $this->assertNotEmpty($currency->name);
        $this->assertContains($currency->diff, [-1, 0, 1]);
        $this->assertContains($currency->change, [-1, 0, 1]);
    }

    public function testDateMethod(): void
    {
        $currenciesNull = NbgCurrency::date();
        $currenciesYesterday = NbgCurrency::date('yesterday');
        $currenciesString = NbgCurrency::date('2022-11-11');
        $currenciesDateTime = NbgCurrency::date(new DateTime('2022-11-11'));
        $currenciesCarbon = NbgCurrency::date(Carbon::today());

        $this->assertInstanceOf(Currencies::class, $currenciesNull);
        $this->assertInstanceOf(Currencies::class, $currenciesYesterday);
        $this->assertInstanceOf(Currencies::class, $currenciesString);
        $this->assertInstanceOf(Currencies::class, $currenciesDateTime);
        $this->assertInstanceOf(Currencies::class, $currenciesCarbon);
    }

    public function testDateEquality(): void
    {
        $currencies1 = NbgCurrency::date('yesterday');
        $currencies2 = NbgCurrency::date(Carbon::yesterday());

        $this->assertEquals($currencies1->date->toDateString(), $currencies2->date->toDateString());
    }

    public function testDateAndGetEquality(): void
    {
        $currency1 = NbgCurrency::date('yesterday')->get('usd');
        $currency2 = NbgCurrency::get('usd', 'yesterday');

        $this->assertEquals($currency1->rate, $currency2->rate);
    }

}
