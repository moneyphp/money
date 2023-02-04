<?php

declare(strict_types=1);

namespace Tests\Money;

use InvalidArgumentException;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

use function json_encode;

use const LC_ALL;
use const PHP_INT_MAX;

/** @covers \Money\Money */
final class MoneyTest extends TestCase
{
    use AggregateExamples;
    use RoundExamples;

    public const AMOUNT = 10;

    public const OTHER_AMOUNT = 5;

    public const CURRENCY = 'EUR';

    public const OTHER_CURRENCY = 'USD';

    /**
     * @psalm-param int|numeric-string $amount
     *
     * @dataProvider equalityExamples
     * @test
     */
    public function itEqualsToAnotherMoney(int|string $amount, Currency $currency, bool $equality): void
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));

        self::assertEquals($equality, $money->equals(new Money($amount, $currency)));
    }

    /** @test */
    public function it_can_compare_currency(): void
    {
        $money1 = new Money(self::AMOUNT, new Currency('USD'));
        $money2 = new Money(self::AMOUNT, new Currency('USD'));
        $money3 = new Money(self::AMOUNT, new Currency('EUR'));

        self::assertTrue($money1->isSameCurrency($money2));
        self::assertTrue($money2->isSameCurrency($money1));
        self::assertFalse($money1->isSameCurrency($money3));
        self::assertFalse($money3->isSameCurrency($money1));
    }

    /**
     * @dataProvider comparisonExamples
     * @test
     */
    public function itComparesTwoAmounts(int $other, int $result): void
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));
        $other = new Money($other, new Currency(self::CURRENCY));

        self::assertEquals($result, $money->compare($other));
        self::assertEquals($result === 1, $money->greaterThan($other));
        self::assertEquals(0 <= $result, $money->greaterThanOrEqual($other));
        self::assertEquals($result === -1, $money->lessThan($other));
        self::assertEquals(0 >= $result, $money->lessThanOrEqual($other));

        if ($result === 0) {
            self::assertEquals($money, $other);
        } else {
            self::assertNotEquals($money, $other);
        }
    }

    /**
     * @psalm-param int|numeric-string $multiplier
     * @psalm-param Money::ROUND_* $roundingMode
     * @psalm-param numeric-string $result
     *
     * @dataProvider roundingExamples
     * @test
     */
    public function itMultipliesTheAmount(int|string $multiplier, int $roundingMode, string $result): void
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->multiply($multiplier, $roundingMode);

        self::assertInstanceOf(Money::class, $money);
        self::assertEquals($result, $money->getAmount());
    }

    /**
     * @test
     */
    public function itMultipliesTheAmountWithLocaleThatUsesCommaSeparator(): void
    {
        $this->setLocale(LC_ALL, 'es_ES.utf8');

        $money = new Money(100, new Currency(self::CURRENCY));
        $money = $money->multiply('0.1');

        self::assertInstanceOf(Money::class, $money);
        self::assertEquals('10', $money->getAmount());
    }

    /**
     * @psalm-param int|numeric-string $divisor
     * @psalm-param Money::ROUND_* $roundingMode
     * @psalm-param numeric-string $result
     *
     * @dataProvider roundingExamples
     * @test
     */
    public function it_divides_the_amount(int|string $divisor, int $roundingMode, string $result): void
    {
        self::assertEquals(
            $result,
            (new Money(1, new Currency(self::CURRENCY)))
                ->multiply($divisor, $roundingMode)
                ->multiply($divisor, $roundingMode)
                ->divide($divisor, $roundingMode)
                ->getAmount(),
            'Our dataset does not contain a lot of data around divisions: we abuse multiplication to verify inverse function properties'
        );
    }

    /**
     * @psalm-param int $amount
     * @psalm-param non-empty-array<positive-int|0|float> $ratios
     * @psalm-param non-empty-array<int> $results
     *
     * @dataProvider allocationExamples
     * @test
     */
    public function itAllocatesAmount(int $amount, array $ratios, array $results): void
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $allocated = $money->allocate($ratios);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            self::assertTrue($money->equals($compareTo));
        }
    }

    /** @test */
    public function it_throws_an_exception_when_allocation_ratio_is_negative(): void
    {
        $money = new Money(100, new Currency(self::CURRENCY));

        $this->expectException(InvalidArgumentException::class);
        /** @psalm-suppress UnusedMethodCall this method throws, but is also considered pure. It's unused by design. */
        $money->allocate([-1]);
    }

    /** @test */
    public function it_throws_an_exception_when_allocation_total_is_zero(): void
    {
        $money = new Money(100, new Currency(self::CURRENCY));

        $this->expectException(InvalidArgumentException::class);
        /** @psalm-suppress UnusedMethodCall this method throws, but is also considered pure. It's unused by design. */
        $money->allocate([0, 0]);
    }

    /**
     * @psalm-param positive-int $amount
     * @psalm-param positive-int $target
     * @psalm-param non-empty-list<positive-int> $results
     *
     * @dataProvider allocationTargetExamples
     * @test
     */
    public function itAllocatesAmountToNTargets(int $amount, int $target, array $results): void
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $allocated = $money->allocateTo($target);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            self::assertTrue($money->equals($compareTo));
        }
    }

    /**
     * @psalm-param int|numeric-string $amount
     *
     * @dataProvider comparatorExamples
     * @test
     */
    public function itHasComparators(int|string $amount, bool $isZero, bool $isPositive, bool $isNegative): void
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        self::assertEquals($isZero, $money->isZero());
        self::assertEquals($isPositive, $money->isPositive());
        self::assertEquals($isNegative, $money->isNegative());
    }

    /**
     * @psalm-param int|numeric-string $amount
     * @psalm-param positive-int|0 $result
     *
     * @dataProvider absoluteExamples
     * @test
     */
    public function itCalculatesTheAbsoluteAmount($amount, $result): void
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $money = $money->absolute();

        self::assertEquals($result, $money->getAmount());
    }

    /**
     * @psalm-param int|numeric-string $amount
     * @psalm-param int $result
     *
     * @dataProvider negativeExamples
     * @test
     */
    public function itCalculatesTheNegativeAmount($amount, $result): void
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $money = $money->negative();

        self::assertEquals($result, $money->getAmount());
    }

    /**
     * @psalm-param positive-int $left
     * @psalm-param positive-int $right
     * @psalm-param numeric-string $expected
     *
     * @dataProvider modExamples
     * @test
     */
    public function itCalculatesTheModulusOfAnAmount($left, $right, $expected): void
    {
        $money      = new Money($left, new Currency(self::CURRENCY));
        $rightMoney = new Money($right, new Currency(self::CURRENCY));

        $money = $money->mod($rightMoney);

        self::assertInstanceOf(Money::class, $money);
        self::assertEquals($expected, $money->getAmount());
    }

    /**
     * @test
     */
    public function itThrowsWhenCalculatingModulusOfDifferentCurrencies(): void
    {
        $money      = new Money(self::AMOUNT, new Currency(self::CURRENCY));
        $rightMoney = new Money(self::OTHER_AMOUNT, new Currency(self::OTHER_CURRENCY));

        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress UnusedMethodCall this method throws */
        $money->mod($rightMoney);
    }

    /**
     * @test
     */
    public function itConvertsToJson(): void
    {
        self::assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );

        self::assertEquals(
            ['amount' => '350', 'currency' => 'EUR'],
            Money::EUR(350)->jsonSerialize()
        );
    }

    /**
     * @test
     */
    public function itSupportsMaxInt(): void
    {
        $one = new Money(1, new Currency('EUR'));

        self::assertInstanceOf(Money::class, new Money(PHP_INT_MAX, new Currency('EUR')));
        self::assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one));
        self::assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->subtract($one));
    }

    /**
     * @test
     */
    public function itReturnsRatioOf(): void
    {
        $currency = new Currency('EUR');
        $zero     = new Money(0, $currency);
        $three    = new Money(3, $currency);
        $six      = new Money(6, $currency);

        self::assertEquals(0, $zero->ratioOf($six));
        self::assertEquals(0.5, $three->ratioOf($six));
        self::assertEquals(1, $three->ratioOf($three));
        self::assertEquals(2, $six->ratioOf($three));
    }

    /**
     * @test
     */
    public function itThrowsWhenCalculatingRatioOfZero(): void
    {
        $currency = new Currency('EUR');
        $zero     = new Money(0, $currency);
        $six      = new Money(6, $currency);

        $this->expectException(InvalidArgumentException::class);

        $six->ratioOf($zero);
    }

    /**
     * @test
     */
    public function itThrowsWhenCalculatingRatioOfDifferentCurrencies(): void
    {
        $money      = new Money(self::AMOUNT, new Currency(self::CURRENCY));
        $rightMoney = new Money(self::OTHER_AMOUNT, new Currency(self::OTHER_CURRENCY));

        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress UnusedMethodCall this method throws */
        $money->ratioOf($rightMoney);
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider sumExamples
     * @test
     */
    public function itCalculatesSum(array $values, Money $sum): void
    {
        self::assertEquals($sum, Money::sum(...$values));
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider minExamples
     * @test
     */
    public function itCalculatesMin(array $values, Money $min): void
    {
        self::assertEquals($min, Money::min(...$values));
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider maxExamples
     * @test
     */
    public function itCalculatesMax(array $values, Money $max): void
    {
        self::assertEquals($max, Money::max(...$values));
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider avgExamples
     * @test
     */
    public function itCalculatesAvg(array $values, Money $avg): void
    {
        self::assertEquals($avg, Money::avg(...$values));
    }

    /**
     * @psalm-param int $amount
     * @psalm-param positive-int|0 $unit
     * @psalm-param int $expected
     * @psalm-param int $roundingMode
     *
     * @test
     * @dataProvider roundToUnitExamples
     */
    public function itRoundsToUnit($amount, $unit, $expected, $roundingMode): void
    {
        self::assertEquals(Money::EUR($expected), Money::EUR($amount)->roundToUnit($unit, $roundingMode));
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int|numeric-string,
     *     Currency,
     *     bool
     * }>
     */
    public function equalityExamples(): array
    {
        return [
            [10, new Currency(self::CURRENCY), true],
            [10, new Currency(self::OTHER_CURRENCY), false],
            [11, new Currency(self::OTHER_CURRENCY), false],
            ['10', new Currency(self::CURRENCY), true],
            ['10.000', new Currency(self::CURRENCY), true],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int,
     *     int
     * }>
     */
    public function comparisonExamples(): array
    {
        return [
            [self::AMOUNT, 0],
            [self::AMOUNT - 1, 1],
            [self::AMOUNT + 1, -1],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int,
     *     non-empty-array<int|string, positive-int|0|float>,
     *     non-empty-array<int|string, int>
     * }>
     */
    public function allocationExamples(): array
    {
        return [
            [100, [1, 1, 1], [34, 33, 33]],
            [101, [1, 1, 1], [34, 34, 33]],
            [5, [3, 7], [2, 3]],
            [5, [7, 3], [4, 1]],
            [5, [7, 3, 0], [4, 1, 0]],
            [-5, [7, 3], [-3, -2]],
            [5, [0, 7, 3], [0, 4, 1]],
            [5, [7, 0, 3], [4, 0, 1]],
            [5, [0, 0, 1], [0, 0, 5]],
            [5, [0, 3, 7], [0, 2, 3]],
            [0, [0, 0, 1], [0, 0, 0]],
            [2, [1, 1, 1], [1, 1, 0]],
            [1, [1, 1], [1, 0]],
            [1, [0.33, 0.66], [0, 1]],
            [101, [3, 7], [30, 71]],
            [101, [7, 3], [71, 30]],
            [101, ['foo' => 7, 'bar' => 3], ['foo' => 71, 'bar' => 30]],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     positive-int,
     *     positive-int,
     *     non-empty-list<positive-int>
     * }>
     */
    public function allocationTargetExamples(): array
    {
        return [
            [15, 2, [8, 7]],
            [10, 2, [5, 5]],
            [15, 3, [5, 5, 5]],
            [10, 3, [4, 3, 3]],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int|numeric-string,
     *     bool,
     *     bool,
     *     bool
     * }>
     */
    public function comparatorExamples(): array
    {
        return [
            [1, false, true, false],
            [0, true, false, false],
            [-1, false, false, true],
            ['1', false, true, false],
            ['0', true, false, false],
            ['-1', false, false, true],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int|numeric-string,
     *     positive-int|0
     * }>
     */
    public function absoluteExamples(): array
    {
        return [
            [1, 1],
            [0, 0],
            [-1, 1],
            ['1', 1],
            ['0', 0],
            ['-1', 1],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int|numeric-string,
     *     int
     * }>
     */
    public function negativeExamples(): array
    {
        return [
            [1, -1],
            [0, 0],
            [-1, 1],
            ['1', -1],
            ['0', 0],
            ['-1', 1],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     positive-int,
     *     positive-int,
     *     numeric-string
     * }>
     */
    public function modExamples(): array
    {
        return [
            [11, 5, '1'],
            [9, 3, '0'],
            [1006, 10, '6'],
            [1007, 10, '7'],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     int,
     *     positive-int|0,
     *     int,
     *     int
     * }>
     */
    public function roundToUnitExamples(): array
    {
        return [
            [510, 2, 500, Money::ROUND_HALF_UP],
            [510, 1, 510, Money::ROUND_HALF_UP],
            [515, 1, 520, Money::ROUND_HALF_UP],
            [4550, 2, 4600, Money::ROUND_HALF_UP],
            [-4550, 2, -4600, Money::ROUND_HALF_UP],
            [-4550, 0, -4550, Money::ROUND_HALF_UP],
            [-4551, 0, -4551, Money::ROUND_HALF_UP],
            [1, 2, 0, Money::ROUND_HALF_UP],
            [5, 2, 0, Money::ROUND_HALF_UP],
            [5, 1, 10, Money::ROUND_HALF_UP],
            [10, 1, 10, Money::ROUND_HALF_UP],
            [10, 8, 0, Money::ROUND_HALF_UP],
            [1250, 2, 1300, Money::ROUND_HALF_UP],
            [1250, 2, 1200, Money::ROUND_HALF_DOWN],
            [1250, 2, 1200, Money::ROUND_HALF_EVEN],
            [1250, 2, 1300, Money::ROUND_HALF_ODD],
            [1250, 2, 1300, Money::ROUND_UP],
            [1250, 2, 1200, Money::ROUND_DOWN],
            [1250, 2, 1300, Money::ROUND_HALF_POSITIVE_INFINITY],
            [1250, 2, 1200, Money::ROUND_HALF_NEGATIVE_INFINITY],
        ];
    }
}
