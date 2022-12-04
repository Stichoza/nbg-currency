<?php
namespace Stichoza\NbgCurrency\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Stichoza\NbgCurrency\NbgCurrency;

class CachingTest extends TestCase
{
    public ReflectionProperty $property;

    protected function setUp(): void
    {
        $reflection = new ReflectionClass(NbgCurrency::class);

        $this->property = $reflection->getProperty('currencies');
    }

    public function testCacheEnabled(): void
    {
        NbgCurrency::disableCaching(); // Clears cache as well
        NbgCurrency::enableCaching();

        $usd = NbgCurrency::get('usd');
        $eur = NbgCurrency::get('eur');

        $this->assertCount(1, $this->property->getValue());

        $usd2 = NbgCurrency::get('usd', Carbon::yesterday());
        $eur2 = NbgCurrency::get('eur', Carbon::yesterday()->subDay());

        $this->assertCount(3, $this->property->getValue());
    }

    public function testCacheDisabled(): void
    {
        NbgCurrency::disableCaching();

        $usd = NbgCurrency::get('usd');
        $eur = NbgCurrency::get('eur');

        $this->assertCount(0, $this->property->getValue());

        $usd2 = NbgCurrency::get('usd', Carbon::yesterday());
        $eur2 = NbgCurrency::get('eur', Carbon::yesterday()->subDay());

        $this->assertCount(0, $this->property->getValue());

        NbgCurrency::enableCaching();
    }
}
