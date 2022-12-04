<?php
namespace Stichoza\NbgCurrency\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Stichoza\NbgCurrency\Data\Currencies;
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

        $this->assertNotNull($usd->rate);
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

        $this->assertNotNull($usd->rate);

        NbgCurrency::enableCaching();
    }

    public function testRequestCount(): void
    {
        CustomNbgCurrency::enableCaching();

        $this->assertEquals(0, CustomNbgCurrency::$requests);

        CustomNbgCurrency::rate('usd');
        CustomNbgCurrency::rate('eur');
        CustomNbgCurrency::rate('gbp');

        $this->assertEquals(1, CustomNbgCurrency::$requests);

        CustomNbgCurrency::disableCaching();

        CustomNbgCurrency::rate('usd');
        CustomNbgCurrency::rate('eur');
        CustomNbgCurrency::rate('gbp');

        $this->assertEquals(4, CustomNbgCurrency::$requests);
    }
}

/**
 * Test class to count HTTP request counts.
 */
class CustomNbgCurrency extends NbgCurrency
{
    public static int $requests = 0;

    protected static function request(Carbon $date, string $language = 'ka', bool $passNullAsDate = false): Currencies
    {
        static::$requests++;
        return parent::request($date, $language, $passNullAsDate);
    }
}
