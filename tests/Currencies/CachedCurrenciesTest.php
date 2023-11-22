<?php

declare(strict_types=1);

namespace Tests\Money\Currencies;

use ArrayIterator;
use Money\Currencies;
use Money\Currencies\CachedCurrencies;
use Money\Currency;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

use function iterator_to_array;

/** @covers \Money\Currencies\CachedCurrencies */
final class CachedCurrenciesTest extends TestCase
{
    /** @test */
    public function it_checks_currencies_using_the_delegated_ones(): void
    {
        $currency = new Currency('EUR');

        $miss              = $this->createMock(CacheItemInterface::class);
        $cache             = $this->createMock(CacheItemPoolInterface::class);
        $wrappedCurrencies = $this->createMock(Currencies::class);

        $miss->method('isHit')
            ->willReturn(false);
        $miss->expects(self::once())
            ->method('set')
            ->with(true);
        $miss->method('get')
            ->willReturn(true);

        $cache->method('getItem')
            ->with('currency|availability|EUR')
            ->willReturn($miss);
        $cache->expects(self::once())
            ->method('save')
            ->with($miss);

        $wrappedCurrencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(true);

        self::assertTrue(
            (new CachedCurrencies($wrappedCurrencies, $cache))
                ->contains($currency)
        );
    }

    /** @test */
    public function it_checks_currencies_from_the_cache(): void
    {
        $currency = new Currency('EUR');

        $hit               = $this->createMock(CacheItemInterface::class);
        $cache             = $this->createMock(CacheItemPoolInterface::class);
        $wrappedCurrencies = $this->createMock(Currencies::class);

        $hit->method('isHit')
            ->willReturn(true);
        $hit->expects(self::never())
            ->method('set');
        $hit->method('get')
            ->willReturn(true);

        $cache->method('getItem')
            ->with('currency|availability|EUR')
            ->willReturn($hit);
        $cache->expects(self::never())
            ->method('save');

        $wrappedCurrencies->expects(self::never())
            ->method('contains');

        self::assertTrue(
            (new CachedCurrencies($wrappedCurrencies, $cache))
                ->contains($currency)
        );
    }

    /** @test */
    public function it_is_iterable(): void
    {
        $refreshed1        = $this->createMock(CacheItemInterface::class);
        $refreshed2        = $this->createMock(CacheItemInterface::class);
        $cache             = $this->createMock(CacheItemPoolInterface::class);
        $wrappedCurrencies = $this->createMock(Currencies::class);

        $refreshed1->expects(self::once())
            ->method('set')
            ->with(true);
        $refreshed2->expects(self::once())
            ->method('set')
            ->with(true);

        $cache->method('getItem')
            ->willReturnMap([
                ['currency|availability|EUR', $refreshed1],
                ['currency|availability|USD', $refreshed2],
            ]);

        $cache->expects(self::exactly(2))
            ->method('save')
            ->with(self::logicalOr($refreshed1, $refreshed2));

        $wrappedCurrencies->expects(self::once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([
                new Currency('EUR'),
                new Currency('USD'),
            ]));

        self::assertEquals(
            [
                new Currency('EUR'),
                new Currency('USD'),
            ],
            iterator_to_array(new CachedCurrencies($wrappedCurrencies, $cache))
        );
    }

    /** @test */
    public function it_checks_subunits_from_the_cache(): void
    {
        $currency = new Currency('EUR');

        $hit               = $this->createMock(CacheItemInterface::class);
        $cache             = $this->createMock(CacheItemPoolInterface::class);
        $wrappedCurrencies = $this->createMock(Currencies::class);

        $hit->method('isHit')
            ->willReturn(true);
        $hit->expects(self::never())
            ->method('set');
        $hit->method('get')
            ->willReturn(2);

        $cache->method('getItem')
            ->with('currency|subunit|EUR')
            ->willReturn($hit);
        $cache->expects(self::never())
            ->method('save');

        $wrappedCurrencies->expects(self::never())
            ->method('subunitFor');

        self::assertEquals(
            2,
            (new CachedCurrencies($wrappedCurrencies, $cache))
                ->subunitFor($currency)
        );
    }

    /** @test */
    public function it_saves_subunits_to_the_cache(): void
    {
        $currency = new Currency('EUR');

        $hit               = $this->createMock(CacheItemInterface::class);
        $cache             = $this->createMock(CacheItemPoolInterface::class);
        $wrappedCurrencies = $this->createMock(Currencies::class);

        $hit->method('isHit')
            ->willReturn(false);
        $hit->expects(self::once())
            ->method('set')
            ->with(2);
        $hit->expects(self::once())
            ->method('get')
            ->willReturn(2);

        $cache->method('getItem')
            ->with('currency|subunit|EUR')
            ->willReturn($hit);
        $cache->expects(self::once())
            ->method('save');

        $wrappedCurrencies->expects(self::once())
            ->method('subunitFor')
            ->willReturn(2);

        self::assertEquals(
            2,
            (new CachedCurrencies($wrappedCurrencies, $cache))
                ->subunitFor($currency)
        );
    }
}
