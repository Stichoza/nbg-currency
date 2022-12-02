<?php
namespace Stichoza\NbgCurrency\Tests;

use BadMethodCallException;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Stichoza\NbgCurrency;

class CurrencyTest extends TestCase
{
    public function testWtfMethod(): void
    {
        $this->expectException(BadMethodCallException::class);

        NbgCurrency::neverGonnaGiveYouUp();
    }

    public function testDate(): void
    {
        $date = NbgCurrency::date();

        $this->assertInstanceOf(Carbon::class, $date, 'The date() method should return Carbon instance.');
    }

    public function testRate()
    {
        $a = NbgCurrency::rate('usd');
        $b = NbgCurrency::rateUsd();

        $this->assertEquals($a, $b, 'Values from magic method and rate() should be the same.');
        $this->assertNotEquals(0, $a, 'Rate should not be zero.');
    }

    public function testName(): void
    {
        $a = NbgCurrency::name('usd');
        $b = NbgCurrency::nameUsd();

        $this->assertEquals($a, $b, 'Values from magic method and name() should be the same.');
        $this->assertNotEquals('', $a, 'Name should not be empty.');
        $this->assertNotNull($a, 'Name should not be null.');
    }

    public function testChange(): void
    {
        $a = NbgCurrency::change('usd');
        $b = NbgCurrency::changeUsd();

        $this->assertEquals($a, $b, 'Values from magic method and change() should be the same.');
        $this->assertContains($a, [-1, 0, 1], 'Change should be either -1, 0, or 1.');
    }

    public function testDiff(): void
    {
        $a = NbgCurrency::diff('usd');
        $b = NbgCurrency::diffUsd();

        $this->assertEquals($a, $b, 'Values from magic method and diff() should be the same.');
    }

    public function testGet(): void
    {
        $object      = NbgCurrency::get('usd');
        $change      = NbgCurrency::change('usd');
        $rate        = NbgCurrency::rate('usd');
        $diff        = NbgCurrency::diff('usd');
        $date        = NbgCurrency::date();
        $name        = NbgCurrency::name('usd');

        $this->assertEquals($object->change, $change);
        $this->assertEquals($object->rate, $rate);
        $this->assertEquals($object->diff, $diff);
        $this->assertEquals($object->name, $name);
        $this->assertEquals($object->date->toDayDateTimeString(), $date->toDayDateTimeString());
    }

    public function testUnsupportedCurrency(): void
    {
        $a = NbgCurrency::isSupported('lol');
        $b = NbgCurrency::diff('lol');

        $this->assertFalse($a, 'Currency "lol" should not be supported.');
        $this->assertEquals(0, $b, 'Diff of non-existing currency should be zero.');
    }
}
