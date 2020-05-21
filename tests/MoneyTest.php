<?php

namespace Tests\Money;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    use AggregateExamples;
    use RoundExamples;

    const AMOUNT = 10;

    const OTHER_AMOUNT = 5;

    const CURRENCY = 'EUR';

    const OTHER_CURRENCY = 'USD';

    /**
     * @dataProvider equalityExamples
     * @test
     */
    public function it_equals_to_another_money($amount, $currency, $equality)
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));

        $this->assertEquals($equality, $money->equals(new Money($amount, $currency)));
    }

    /**
     * @dataProvider comparisonExamples
     * @test
     */
    public function it_compares_two_amounts($other, $result)
    {
        $money = new Money(self::AMOUNT, new Currency(self::CURRENCY));
        $other = new Money($other, new Currency(self::CURRENCY));

        $this->assertEquals($result, $money->compare($other));
        $this->assertEquals(1 === $result, $money->greaterThan($other));
        $this->assertEquals(0 <= $result, $money->greaterThanOrEqual($other));
        $this->assertEquals(-1 === $result, $money->lessThan($other));
        $this->assertEquals(0 >= $result, $money->lessThanOrEqual($other));

        if ($result === 0) {
            $this->assertEquals($money, $other);
        } else {
            $this->assertNotEquals($money, $other);
        }
    }

    /**
     * @dataProvider roundExamples
     * @test
     */
    public function it_multiplies_the_amount($multiplier, $roundingMode, $result)
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->multiply($multiplier, $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string) $result, $money->getAmount());
    }

    /**
     * @test
     */
    public function it_multiplies_the_amount_with_locale_that_uses_comma_separator()
    {
        $this->setLocale(LC_ALL, 'es_ES.utf8');

        $money = new Money(100, new Currency(self::CURRENCY));
        $money = $money->multiply(10 / 100);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals(10, $money->getAmount());
    }

    /**
     * @dataProvider invalidOperandExamples
     * @test
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_multiplication($operand)
    {
        $this->expectException(\InvalidArgumentException::class);

        $money = new Money(1, new Currency(self::CURRENCY));

        $money->multiply($operand);
    }

    /**
     * @dataProvider roundExamples
     */
    public function it_divides_the_amount($divisor, $roundingMode, $result)
    {
        $money = new Money(1, new Currency(self::CURRENCY));

        $money = $money->divide(1 / $divisor, $roundingMode);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals((string) $result, $money->getAmount());
    }

    /**
     * @dataProvider invalidOperandExamples
     * @test
     */
    public function it_throws_an_exception_when_operand_is_invalid_during_division($operand)
    {
        $this->expectException(\InvalidArgumentException::class);

        $money = new Money(1, new Currency(self::CURRENCY));

        $money->divide($operand);
    }

    /**
     * @dataProvider allocationExamples
     * @test
     */
    public function it_allocates_amount($amount, $ratios, $results)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $allocated = $money->allocate($ratios);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            $this->assertEquals($money, $compareTo);
        }
    }

    /**
     * @dataProvider allocationTargetExamples
     * @test
     */
    public function it_allocates_amount_to_n_targets($amount, $target, $results)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $allocated = $money->allocateTo($target);

        foreach ($allocated as $key => $money) {
            $compareTo = new Money($results[$key], $money->getCurrency());

            $this->assertEquals($money, $compareTo);
        }
    }

    /**
     * @dataProvider comparatorExamples
     * @test
     */
    public function it_has_comparators($amount, $isZero, $isPositive, $isNegative)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $this->assertEquals($isZero, $money->isZero());
        $this->assertEquals($isPositive, $money->isPositive());
        $this->assertEquals($isNegative, $money->isNegative());
    }

    /**
     * @dataProvider absoluteExamples
     * @test
     */
    public function it_calculates_the_absolute_amount($amount, $result)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $money = $money->absolute();

        $this->assertEquals($result, $money->getAmount());
    }

    /**
     * @dataProvider negativeExamples
     * @test
     */
    public function it_calculates_the_negative_amount($amount, $result)
    {
        $money = new Money($amount, new Currency(self::CURRENCY));

        $money = $money->negative();

        $this->assertEquals($result, $money->getAmount());
    }

    /**
     * @dataProvider modExamples
     * @test
     */
    public function it_calculates_the_modulus_of_an_amount($left, $right, $expected)
    {
        $money = new Money($left, new Currency(self::CURRENCY));
        $rightMoney = new Money($right, new Currency(self::CURRENCY));

        $money = $money->mod($rightMoney);

        $this->assertInstanceOf(Money::class, $money);
        $this->assertEquals($expected, $money->getAmount());
    }

    /**
     * @test
     */
    public function it_converts_to_json()
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
    public function it_supports_max_int()
    {
        $one = new Money(1, new Currency('EUR'));

        $this->assertInstanceOf(Money::class, new Money(PHP_INT_MAX, new Currency('EUR')));
        $this->assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->add($one));
        $this->assertInstanceOf(Money::class, (new Money(PHP_INT_MAX, new Currency('EUR')))->subtract($one));
    }

    /**
     * @test
     */
    public function it_returns_ratio_of()
    {
        $currency = new Currency('EUR');
        $zero = new Money(0, $currency);
        $three = new Money(3, $currency);
        $six = new Money(6, $currency);

        $this->assertEquals(0, $zero->ratioOf($six));
        $this->assertEquals(0.5, $three->ratioOf($six));
        $this->assertEquals(1, $three->ratioOf($three));
        $this->assertEquals(2, $six->ratioOf($three));
    }

    /**
     * @test
     */
    public function it_throws_when_calculating_ratio_of_zero()
    {
        $this->expectException(\InvalidArgumentException::class);

        $currency = new Currency('EUR');
        $zero = new Money(0, $currency);
        $six = new Money(6, $currency);

        $six->ratioOf($zero);
    }

    /**
     * @dataProvider sumExamples
     * @test
     */
    public function it_calculates_sum($values, $sum)
    {
        $this->assertEquals($sum, Money::sum(...$values));
    }

    /**
     * @dataProvider minExamples
     * @test
     */
    public function it_calculates_min($values, $min)
    {
        $this->assertEquals($min, Money::min(...$values));
    }

    /**
     * @dataProvider maxExamples
     * @test
     */
    public function it_calculates_max($values, $max)
    {
        $this->assertEquals($max, Money::max(...$values));
    }

    /**
     * @dataProvider avgExamples
     * @test
     */
    public function it_calculates_avg($values, $avg)
    {
        $this->assertEquals($avg, Money::avg(...$values));
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_min_with_zero_arguments()
    {
        $this->expectException(\Throwable::class);
        Money::min(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_max_with_zero_arguments()
    {
        $this->expectException(\Throwable::class);
        Money::max(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_sum_with_zero_arguments()
    {
        $this->expectException(\Throwable::class);
        Money::sum(...[]);
    }

    /**
     * @test
     * @requires PHP 7.0
     */
    public function it_throws_when_calculating_avg_with_zero_arguments()
    {
        $this->expectException(\Throwable::class);
        Money::avg(...[]);
    }

    public function equalityExamples()
    {
        return [
            [self::AMOUNT, new Currency(self::CURRENCY), true],
            [self::AMOUNT + 1, new Currency(self::CURRENCY), false],
            [self::AMOUNT, new Currency(self::OTHER_CURRENCY), false],
            [self::AMOUNT + 1, new Currency(self::OTHER_CURRENCY), false],
            [(string) self::AMOUNT, new Currency(self::CURRENCY), true],
            [((string) self::AMOUNT).'.000', new Currency(self::CURRENCY), true],
        ];
    }

    public function comparisonExamples()
    {
        return [
            [self::AMOUNT, 0],
            [self::AMOUNT - 1, 1],
            [self::AMOUNT + 1, -1],
        ];
    }

    public function invalidOperandExamples()
    {
        return [
            [[]],
            [false],
            ['operand'],
            [null],
            [new \stdClass()],
        ];
    }

    public function allocationExamples()
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

    public function allocationTargetExamples()
    {
        return [
            [15, 2, [8, 7]],
            [10, 2, [5, 5]],
            [15, 3, [5, 5, 5]],
            [10, 3, [4, 3, 3]],
        ];
    }

    public function comparatorExamples()
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

    public function absoluteExamples()
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

    public function negativeExamples()
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

    public function modExamples()
    {
        return [
            [11, 5, '1'],
            [9, 3, '0'],
            [1006, 10, '6'],
            [1007, 10, '7'],
        ];
    }
}
