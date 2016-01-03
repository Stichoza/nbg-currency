<?php
namespace Stichoza\NbgCurrency\Tests;

use BadMethodCallException;
use Carbon\Carbon;
use Stichoza\NbgCurrency\NbgCurrency;

class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //
    }

    /**
     * @expectedException        BadMethodCallException
     * @expectedExceptionMessage Method [neverGonnaGiveYouUp] does not exist
     */
    public function testWtfMethod()
    {
        NbgCurrency::neverGonnaGiveYouUp();
    }

    public function testDate()
    {
        $date = NbgCurrency::date();
        $this->assertInstanceOf(Carbon::class, $date);
    }

    public function testRate()
    {
        $a = NbgCurrency::rate('usd');
        $b = NbgCurrency::rateUsd();
        $this->assertEquals($a, $b);
        $this->assertNotEquals($a, 0);
        $this->assertInternalType('double', $a);
    }

    public function testDescription()
    {
        $a = NbgCurrency::description('usd');
        $b = NbgCurrency::descriptionUsd();
        $this->assertEquals($a, $b);
        $this->assertNotEquals($a, '');
        $this->assertInternalType('string', $a);
    }

    public function testChange()
    {
        $a = NbgCurrency::change('usd');
        $b = NbgCurrency::changeUsd();
        $this->assertEquals($a, $b);
        $this->assertTrue(in_array($a, [-1, 0, 1]));
        $this->assertInternalType('int', $a);
    }

    public function testDiff()
    {
        $a = NbgCurrency::diff('usd');
        $b = NbgCurrency::diffUsd();
        $this->assertEquals($a, $b);
        $this->assertInternalType('double', $a);
    }

    public function testGet()
    {
        $object      = NbgCurrency::get('usd');
        $change      = NbgCurrency::change('usd');
        $rate        = NbgCurrency::rate('usd');
        $diff        = NbgCurrency::diff('usd');
        $date        = NbgCurrency::date();
        $description = NbgCurrency::description('usd');
        $this->assertEquals($object->change, $change);
        $this->assertEquals($object->rate, $rate);
        $this->assertEquals($object->diff, $diff);
        $this->assertEquals($object->description, $description);
        $this->assertEquals($object->date->toDayDateTimeString(), $date->toDayDateTimeString());
    }

    public function testUnsupportedCurrency()
    {
        $a = NbgCurrency::isSupported('lol');
        $b = NbgCurrency::diff('lol');
        $this->assertFalse($a);
        $this->assertEquals($b, 0);
    }
}
