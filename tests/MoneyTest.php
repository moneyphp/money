<?php

declare(strict_types=1);

namespace Tests\Money;

use InvalidArgumentException;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Throwable;

use function json_encode;

use const LC_ALL;
use const PHP_INT_MAX;

final class MoneyTest extends TestCase
{
    use AggregateExamples;
    use RoundExamples;

    public const AMOUNT = 10;

    public const OTHER_AMOUNT = 5;

    public const CURRENCY = 'EUR';

    public const OTHER_CURRENCY = 'USD';

    /**
     * @dataProvider equalityExamples
     * @test
     *
     * @psalm-param int|numeric-string $amount
     */
    public function itEqualsToAnotherMoney(int|string $amount, Currency $currency, bool $equality): void
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));

        $this->assertEquals($equality, $money->equals(new Money($amount, $currency)));
    }

    /**
     * @dataProvider comparisonExamples
     * @test
     */
    public function itComparesTwoAmounts(int $other, int $result): void
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));
        $other = new Money($other, new Currency(self::CURRENCY));

        $this->assertEquals($result, $money->compare($other));
        $this->assertEquals($result === 1, $money->greaterThan($other));
        $this->assertEquals(0 <= $result, $money->greaterThanOrEqual($other));
        $this->assertEquals($result === -1, $money->lessThan($other));
        $this->assertEquals(0 >= $result, $money->lessThanOrEqual($other));

        if ($result === 0) {
            $this->assertEquals($money, $other);
        } else {
            $this->assertNotEquals($money, $other);
        }
    }

    /**
     * @psalm-param numeric-string $multiplier
     * @psalm-param Money::ROUND_* $roundingMode
     * @psalm-param numeric-string $result
     *
     * @dataProvider roundingExamples
     * @test
     */
    public function itMultipliesTheAmount(string $multiplier, int $roundingMode, string $result): void
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->multiply($multiplier, $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($result, $money->getAmount());
    }

    /**
     * @test
     */
    public function itMultipliesTheAmountWithLocaleThatUsesCommaSeparator(): void
    {
        $this->setLocale(LC_ALL, 'es_ES.utf8');

        $money = new Money(100, new Currency(self::CURRENCY));
        $money = $money->multiply('0.1');

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals('10', $money->getAmount());
    }

    /**
     * @psalm-param float|int|numeric-string $divisor
     * @psalm-param Money::ROUND_* $roundingMode
     * @psalm-param numeric-string $result
     *
     * @dataProvider roundingExamples
     */
    public function it_divides_the_amount(float|int|string $divisor, int $roundingMode, string $result): void
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->divide((string) (1 / $divisor), $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($result, $money->getAmount());
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

            $this->assertTrue($money->equals($compareTo));
        }
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

            $this->assertTrue($money->equals($compareTo));
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

        $this->assertEquals($isZero, $money->isZero());
        $this->assertEquals($isPositive, $money->isPositive());
        $this->assertEquals($isNegative, $money->isNegative());
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

        $this->assertEquals($result, $money->getAmount());
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

        $this->assertEquals($result, $money->getAmount());
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

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expected, $money->getAmount());
    }

    /**
     * @test
     */
    public function itConvertsToJson(): void
    {
        $this->assertEquals(
            '{"amount":"350","currency":"EUR"}',
            json_encode(Money::EUR(350))
        );

        $this->assertEquals(
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

        $this->assertInstanceOf(Money::class, new Money(PHP_INT_MAX, new Currency('EUR')));
        $this->assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one));
        $this->assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->subtract($one));
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

        $this->assertEquals(0, $zero->ratioOf($six));
        $this->assertEquals(0.5, $three->ratioOf($six));
        $this->assertEquals(1, $three->ratioOf($three));
        $this->assertEquals(2, $six->ratioOf($three));
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

        /** @psalm-suppress UnusedMethodCall this method throws, but is also considered pure. It's unused by design. */
        $six->ratioOf($zero);
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider sumExamples
     * @test
     */
    public function itCalculatesSum(array $values, Money $sum): void
    {
        $this->assertEquals($sum, Money::sum(...$values));
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider minExamples
     * @test
     */
    public function itCalculatesMin(array $values, Money $min): void
    {
        $this->assertEquals($min, Money::min(...$values));
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider maxExamples
     * @test
     */
    public function itCalculatesMax(array $values, Money $max): void
    {
        $this->assertEquals($max, Money::max(...$values));
    }

    /**
     * @psalm-param non-empty-list<Money> $values
     *
     * @dataProvider avgExamples
     * @test
     */
    public function itCalculatesAvg(array $values, Money $avg): void
    {
        $this->assertEquals($avg, Money::avg(...$values));
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function itThrowsWhenCalculatingMinWithZeroArguments(): void
    {
        $this->expectException(Throwable::class);
        Money::min(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function itThrowsWhenCalculatingMaxWithZeroArguments(): void
    {
        $this->expectException(Throwable::class);
        Money::max(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function itThrowsWhenCalculatingSumWithZeroArguments(): void
    {
        $this->expectException(Throwable::class);
        Money::sum(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function itThrowsWhenCalculatingAvgWithZeroArguments(): void
    {
        $this->expectException(Throwable::class);
        Money::avg(...[]);
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
     *
     * @psalm-suppress LessSpecificReturnStatement type inference for `array<string, T>` fails to find non-empty-array for the last item
     * @psalm-suppress MoreSpecificReturnType type inference for `array<string, T>` fails to find non-empty-array for the last item
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
}
