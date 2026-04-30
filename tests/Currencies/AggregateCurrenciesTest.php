<?php

declare(strict_types=1);

namespace Tests\Money\Currencies;

use ArrayIterator;
use Money\Currencies;
use Money\Currencies\AggregateCurrencies;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

/** @covers \Money\Currencies\AggregateCurrencies */
final class AggregateCurrenciesTest extends TestCase
{
    /** @test */
    public function it_contains_currencies(): void
    {
        $currencies      = $this->createMock(Currencies::class);
        $otherCurrencies = $this->createMock(Currencies::class);
        $currency        = new Currency('EUR');

        $currencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(false);
        $otherCurrencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(true);

        self::assertTrue(
            (new AggregateCurrencies([$currencies, $otherCurrencies]))
                ->contains($currency)
        );
    }

    /** @test */
    public function it_might_not_contain_currencies(): void
    {
        $currencies      = $this->createMock(Currencies::class);
        $otherCurrencies = $this->createMock(Currencies::class);
        $currency        = new Currency('EUR');

        $currencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(false);
        $otherCurrencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(false);

        self::assertFalse(
            (new AggregateCurrencies([$currencies, $otherCurrencies]))
                ->contains($currency)
        );
    }

    /** @test */
    public function it_provides_subunit(): void
    {
        $currencies      = $this->createMock(Currencies::class);
        $otherCurrencies = $this->createMock(Currencies::class);
        $currency        = new Currency('EUR');

        $currencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(false);
        $otherCurrencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(true);
        $currencies->expects(self::never())
            ->method('subunitFor');
        $otherCurrencies->method('subunitFor')
            ->with(self::equalTo($currency))
            ->willReturn(2);

        self::assertSame(
            2,
            (new AggregateCurrencies([$currencies, $otherCurrencies]))
                ->subunitFor($currency)
        );
    }

    /** @test */
    public function it_throws_an_exception_when_providing_subunit_and_currency_is_unknown(): void
    {
        $currencies      = $this->createMock(Currencies::class);
        $otherCurrencies = $this->createMock(Currencies::class);
        $currency        = new Currency('EUR');

        $currencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(false);
        $otherCurrencies->method('contains')
            ->with(self::equalTo($currency))
            ->willReturn(false);
        $currencies->expects(self::never())
            ->method('subunitFor');
        $otherCurrencies->expects(self::never())
            ->method('subunitFor');

        $aggregate = new AggregateCurrencies([$currencies, $otherCurrencies]);

        $this->expectException(UnknownCurrencyException::class);
        $aggregate->subunitFor($currency);
    }

    /** @test */
    public function it_is_iterable(): void
    {
        $currencies      = $this->createMock(Currencies::class);
        $otherCurrencies = $this->createMock(Currencies::class);

        $currencies->method('getIterator')
            ->willReturn(new ArrayIterator([new Currency('EUR')]));
        $otherCurrencies->method('getIterator')
            ->willReturn(new ArrayIterator([new Currency('USD')]));

        self::assertEquals(
            [
                new Currency('EUR'),
                new Currency('USD'),
            ],
            iterator_to_array(new AggregateCurrencies([$currencies, $otherCurrencies]), false)
        );
    }

    /** @test */
    public function it_can_operate_be_rewinded_and_reused(): void
    {
        $currencies      = $this->createMock(Currencies::class);
        $otherCurrencies = $this->createMock(Currencies::class);

        $currencies->method('getIterator')
            ->willReturn(new ArrayIterator([new Currency('EUR')]));
        $otherCurrencies->method('getIterator')
            ->willReturn(new ArrayIterator([new Currency('USD')]));

        $expectedCurrencies = [
            new Currency('EUR'),
            new Currency('USD'),
        ];
        $iterator           = (new AggregateCurrencies([$currencies, $otherCurrencies]))
            ->getIterator();

        self::assertEquals(
            $expectedCurrencies,
            iterator_to_array($iterator, false)
        );
        self::assertEquals(
            $expectedCurrencies,
            iterator_to_array($iterator, false),
            'Can re-use the previous iteration'
        );
    }
}
